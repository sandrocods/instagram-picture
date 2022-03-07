<?php
/*
======================= START OF LICENSE NOTICE =======================
Copyright (C) 2022 sandroputraa. All Rights Reserved

NO WARRANTY. THE PRODUCT IS PROVIDED BY DEVELOPER "AS IS" AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL DEVELOPER BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER
IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THE PRODUCT, EVEN
IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
======================== END OF LICENSE NOTICE ========================
Primary Author: sandroputraa

 */

namespace sandroputraa\InstagramPicture;

class Instagram
{
    public $UserAgent;
    public $login_data;

    /**
     * Generate a random user agent
     */
    public function Generate_UserAgent()
    {
        $this->UserAgent = \Campo\UserAgent::random([
            'os_type' => 'Windows',
            'device_type' => 'Mobile',
        ]);
    }

    /**
     * Save the session data to a file
     *
     * @param username The username of the user.
     */
    public function Save_Sessions($username)
    {

        if (empty($username)) {
            throw new Exception("Username Empty");
        }

        Helpers::save(__DIR__ . '/sessions/' . $username . '.json', json_encode($this->login_data), 'a');

    }

    /**
     * Get the cookie from the Instagram API
     *
     * @return The cookie is being returned.
     */
    public function GetCookie()
    {

        // User Agent
        $this->Generate_UserAgent();
        $request = Helpers::Curl(
            Constants::API,
            'GET',
            null,
            null,
            [
                "user-agent" => $this->UserAgent,
            ]
        );

        if (!empty($request['Cookie']['csrftoken'] && $request['Cookie']['mid'] && $request['Cookie']['ig_did'])) {

            return [
                'Status' => true,
                'Data' => [
                    'Cookie' => $request['Cookie'],
                ],
            ];
        } else {
            return [
                'Status' => false,
                'Msg' => 'Failed Get Cookie',
            ];
        }
    }

    /**
     * Login to Instagram with the given username and password
     *
     * @param username Your Instagram username.
     * @param password The password of the account you want to login.
     *
     * @return The login function returns a login_data array.
     */
    public function Login($username, $password)
    {

        if (empty($username && $password)) {

            throw new Exception("Username and Password Empty !");
        }

        $RequestCookies = $this->GetCookie();
        if ($RequestCookies['Status'] === true) {

            $RequestLogin = Helpers::curl(
                Constants::API . 'accounts/login/ajax/',
                'POST',
                [
                    'enc_password' => '#PWD_INSTAGRAM_BROWSER:0:' . time() . ':' . $password,
                    'username' => $username,
                    'queryParams' => '{}',
                    'optIntoOneTap' => false,
                    'stopDeletionNonce' => '',
                    'trustedDeviceRecords' => '{}',
                ],
                null,
                array_merge(
                    Constants::static_headers, [
                        'X-CSRFToken' => $RequestCookies['Data']['Cookie']['csrftoken'],
                        'User-Agent' => $this->UserAgent,
                    ]
                ),
                $RequestCookies['Data']['Cookie']
            );

            if (json_decode($RequestLogin['Body'], true)['authenticated'] === true || json_decode($RequestLogin['Body'], true)['status'] == 'ok') {

                $this->login_data = [
                    'Status' => true,
                    'Data' => [
                        'OldCookie' => $RequestCookies['Data']['Cookie'],
                        'NewCookie' => $RequestLogin['Cookie'],
                        'x-ig-set-www-claim' => $RequestLogin['Headers']['x-ig-set-www-claim'],
                    ],
                ];

                $this->Save_Sessions($username);

                return $this->login_data;

            } else {

                $this->login_data = [
                    'Status' => false,
                    'Msg' => $RequestLogin['Body'],
                ];

                return $this->login_data;

            }
        }

    }

    /**
     * Change the profile picture of the account
     *
     * @param picture_file The path to the image file.
     *
     * @return The return value is an array with two keys: Status and Data. Status is a boolean value that
     * indicates whether the request was successful or not. Data contains the data returned by the request.
     */
    public function ChangeProfile($picture_file)
    {

        if (!file_exists($picture_file)) {
            throw new Exception("Photo File not found !");
        }

        $file_picture = curl_file_create($picture_file);
        $RequestChangePicture = Helpers::curl(
            Constants::API . 'accounts/web_change_profile_picture/',
            'POST',
            [
                'profile_pic' => $file_picture,
            ],
            null,
            array_merge(
                Constants::static_headers, [
                    'X-CSRFToken' => $this->login_data['Data']['NewCookie']['csrftoken'],
                    'User-Agent' => $this->UserAgent,
                    'Content-Type' => 'multipart/form-data',
                ]
            ),
            $this->login_data['Data']['NewCookie']
        );

        if (json_decode($RequestChangePicture['Body'], true)['status'] == 'ok') {

            return [
                'Status' => true,
                'Data' => [
                    'profile_pic_url_hd' => json_decode($RequestChangePicture['Body'], true)['profile_pic_url_hd'],
                ],
            ];

        } else {

            return [
                'Status' => false,
                'Msg' => $RequestChangePicture['Body'],
            ];
        }

    }

    /**
     * Upload a single photo to Instagram
     *
     * @param picture_file The path to the image file you want to upload.
     * @param caption The caption for the photo.
     */
    public function UploadSingleFeeds($picture_file, $caption = '')
    {

        if (!file_exists($picture_file)) {
            throw new Exception("Photo File not found !");
        }

        $fbUploader = $this->_fb_uploader($picture_file);
        if ($fbUploader['Status'] === true) {

            $RequestUploadSingle = Helpers::curl(
                Constants::API2 . 'api/v1/media/configure/',
                'POST',
                [
                    'source_type' => 'library',
                    'caption' => $caption,
                    'upcoming_event' => '',
                    'upload_id' => $fbUploader['Data']['upload_id'],
                    'usertags' => '',
                    'custom_accessibility_caption' => '',
                    'disable_comments' => 0,
                    'like_and_view_counts_disabled' => 0,
                    'igtv_ads_toggled_on' => '',
                    'igtv_share_preview_to_feed' => 1,
                    'is_unified_video' => 1,
                    'video_subtitles_enabled' => 0,
                ],
                null,
                array_merge(
                    Constants::static_headers2, [
                        'X-CSRFToken' => $this->login_data['Data']['NewCookie']['csrftoken'],
                        'User-Agent' => $this->UserAgent,
                    ]
                ),
                array_merge(
                    $this->login_data['Data']['NewCookie'],
                    [
                        'mid' => $this->login_data['Data']['OldCookie']['mid'],
                        'ig_did' => $this->login_data['Data']['OldCookie']['ig_did'],
                    ]
                )
            );

            if (json_decode($RequestUploadSingle['Body'], true)['status'] == 'ok') {

                return [
                    'Status' => true,
                    'Data' => [
                        'code' => json_decode($RequestUploadSingle['Body'], true)['media']['code'],
                        'id' => json_decode($RequestUploadSingle['Body'], true)['media']['id'],
                        'caption' => json_decode($RequestUploadSingle['Body'], true)['media']['caption']['text'],
                    ],
                ];
            } else {
                return [
                    'Status' => false,
                    'Msg' => $RequestUploadSingle['Body'],
                ];
            }

        }

    }

    /**
     * Uploads a photo to Instagram
     *
     * @param picture_file The path to the image file you want to upload.
     *
     * @return The return value is an array with two keys: Status and Data. Status is a boolean value
     * that indicates whether the upload was successful or not. Data contains the upload_id that is
     * used to upload the photo.
     */
    public function _fb_uploader($picture_file)
    {

        if (!file_exists($picture_file)) {
            throw new Exception("Photo File not found !");
        }

        $getImage = file_get_contents($picture_file);

        $upload_id = Helpers::generateUploadId();

        $Requestfbuploader = Helpers::curl(
            Constants::API2 . 'rupload_igphoto/fb_uploader_' . $upload_id,
            'POST',
            $getImage,
            null,
            array_merge(
                Constants::static_headers2, [
                    'X-CSRFToken' => $this->login_data['Data']['NewCookie']['csrftoken'],
                    'User-Agent' => $this->UserAgent,
                    'X-Entity-Type' => 'image/jpeg',
                    'X-Entity-Name' => 'fb_uploader_' . $upload_id,
                    'Offset' => '0',
                    'X-Instagram-Rupload-Params' => '{"media_type":1,"upload_id":"' . $upload_id . '","upload_media_height":500,"upload_media_width":500}',
                    'X-Entity-Length' => filesize($picture_file),
                ]
            ),
            array_merge(
                $this->login_data['Data']['NewCookie'],
                [
                    'mid' => $this->login_data['Data']['OldCookie']['mid'],
                    'ig_did' => $this->login_data['Data']['OldCookie']['ig_did'],
                ]
            )
        );

        if (json_decode($Requestfbuploader['Body'], true)['status'] == 'ok') {

            return [
                'Status' => true,
                'Data' => [
                    'upload_id' => json_decode($Requestfbuploader['Body'], true)['upload_id'],
                ],
            ];

        } else {

            return [
                'Status' => false,
                'Msg' => $Requestfbuploader,
            ];
        }

    }
}
