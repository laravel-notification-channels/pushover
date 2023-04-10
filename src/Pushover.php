<?php

namespace NotificationChannels\Pushover;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use NotificationChannels\Pushover\Exceptions\CouldNotSendNotification;
use NotificationChannels\Pushover\Exceptions\ServiceCommunicationError;

class Pushover
{
    /**
     * Maximum size of the image attachment in bytes accepted by the API (https://pushover.net/api#attachments).
     *
     * @var int
     */
    protected const IMAGE_SIZE_LIMIT = 2621440;

    /**
     * Location of the Pushover API.
     *
     * @var string
     */
    protected $pushoverApiUrl = 'https://api.pushover.net/1/messages.json';

    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Pushover App Token.
     *
     * @var string
     */
    protected $token;

    /**
     * @param  HttpClient  $http
     * @param  string  $token
     */
    public function __construct(HttpClient $http, $token)
    {
        $this->http = $http;

        $this->token = $token;
    }

    /**
     * Send Pushover message.
     *
     * @link  https://pushover.net/api
     *
     * @param  array  $params
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws CouldNotSendNotification
     */
    public function send($params)
    {
        try {
            $multipart = [];

            foreach ($this->paramsWithToken($params) as $name => $contents) {
                if ($name !== 'image') {
                    $multipart[] = [
                        'name'     => $name,
                        'contents' => $contents,
                    ];
                } else {
                    $image = $this->getImageData($contents);

                    if ($image) {
                        $multipart[] = $image;
                    }
                }
            }

            return $this->http->post(
                $this->pushoverApiUrl,
                [
                    'multipart' => $multipart,
                ]
            );
        } catch (RequestException $exception) {
            if ($exception->getResponse()) {
                throw CouldNotSendNotification::serviceRespondedWithAnError($exception->getResponse());
            }
            throw ServiceCommunicationError::communicationFailed($exception);
        } catch (Exception $exception) {
            throw ServiceCommunicationError::communicationFailed($exception);
        }
    }

    /**
     * Merge token into parameters array, unless it has been set on the PushoverReceiver.
     *
     * @param  array  $params
     * @return array
     */
    protected function paramsWithToken($params)
    {
        return array_merge([
            'token' => $this->token,
        ], $params);
    }

    /**
     * Build the multipart array information for the attached image.
     *
     * If there is any error (problem with reading the file, file size exceeds the limit, the file is not an image),
     * silently returns null and sends the message without image attachment.
     *
     * @param $file
     * @return array|null
     */
    private function getImageData($file): ?array
    {
        try {
            // check if $file is not too big
            if (is_file($file) && is_readable($file)) {
                // directly check server file size
                if (filesize($file) > self::IMAGE_SIZE_LIMIT) {
                    return null;
                }

                $fileSizeChecked = true;
            } else {
                // check "Content-Length" header even before downloading the file
                $response = $this->http->request('GET', $file, ['stream' => true]);
                $contentLength = $response->getHeader('Content-Length')[0] ?? null;

                if (isset($contentLength) && $contentLength > self::IMAGE_SIZE_LIMIT) {
                    return null;
                }

                // some servers may not return the "Content-Length" header
                $fileSizeChecked = (bool) $contentLength;
            }

            // check if $file is an image
            $imageType = exif_imagetype($file);
            if ($imageType === false) {
                return null;
            }
            $contentType = image_type_to_mime_type($imageType);

            $contents = file_get_contents($file);
            // if not checked before, finally check the file size after reading it
            if (! $fileSizeChecked && strlen($contents) > self::IMAGE_SIZE_LIMIT) {
                return null;
            }
        } catch (Exception $exception) {
            return null;
        }

        return [
            // name of the field holding the image must be 'attachment' (https://pushover.net/api#attachments)
            'name'     => 'attachment',
            'contents' => $contents,
            'filename' => basename($file),
            'headers'  => [
                'Content-Type' => $contentType,
            ],
        ];
    }
}
