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

use Curl\Curl;

class Helpers
{

    /**
     * This function is used to make HTTP requests using the PHP Curl library.
     *
     * @param url The URL to send the request to.
     * @param method The HTTP method to use.
     * @param postfields The data to be posted to the server.
     * @param followlocation If set to true, curl will follow any "Location: " header that the server sends
     * as part of the HTTP header.
     * @param headers An array of headers to send with the request.
     * @param setCookie Set Cookie
     *
     * @return The return value is an array with the following keys:
     */
    public static function Curl(
        $url,
        $method,
        $postfields = null,
        $followlocation = null,
        $headers = null,
        $setCookie = null
    ) {
        $curl = new Curl();

        //Set Headers
        foreach ($headers as $key => $value) {
            $curl->setHeader($key, $value);
        }

        // Set Cookie
        if (!empty($setCookie)) {

            foreach ($setCookie as $key => $value) {
                $curl->setCookie($key, $value);
            }
        }

        //Set Follow Location
        if ($followlocation !== null) {
            $curl->setFollowLocation();
            $curl->setMaximumRedirects($followlocation['max']);
        }

        // Post Method
        if (strtoupper($method) == "POST") {

            $curl->post($url, $postfields);

        } elseif (strtoupper($method) == "GET") {

            $curl->get($url);
        }
        $curl->close();
        if ($curl->error) {
            return [
                'Status' => false,
                'Msg' => $curl->getErrorMessage(),
                'HttpCode' => $curl->getHttpStatusCode(),
                'RawResponse' => $curl->getRawResponse(),
                'RequestHeaders' => $curl->getRequestHeaders(),

            ];
        } else {
            return [
                'Status' => true,
                'HttpCode' => $curl->getHttpStatusCode(),
                'Body' => $curl->getRawResponse(),
                'Headers' => $curl->getResponseHeaders(),
                'Cookie' => $curl->getResponseCookies(),
            ];
        }
    }

    /**
     * Save a line of text to a file
     *
     * @param fileName The name of the file to be created.
     * @param line The line to be written to the file.
     * @param opt The file mode.
     */
    public static function save($fileName, $line, $opt)
    {
        $file = fopen($fileName, $opt);
        fwrite($file, $line);
        fclose($file);
    }

    /**
     * Generate a random number that is a timestamp in milliseconds
     *
     * @return A string of numbers and characters.
     */
    public static function generateUploadId()
    {
        $result = null;
        $result = number_format(round(microtime(true) * 1000), 0, '', '');
        return $result;
    }

}
