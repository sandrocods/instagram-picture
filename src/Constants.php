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

class Constants
{
    const API = 'https://www.instagram.com/';
    const API2 = 'https://i.instagram.com/';

    const static_headers =  [
        "Accept" => "*/*",
        "Accept-Language" => "en-US,en;q=0.9,id;q=0.8",
        "Connection" => "keep-alive",
        "Host" => "www.instagram.com",
        "Origin" => "https://www.instagram.com",
        "Referer" => "https://www.instagram.com/",
        "Sec-Fetch-Dest" => "empty",
        "Sec-Fetch-Mode" => "cors",
        "Sec-Fetch-Site" => "same-origin",
        "X-ASBD-ID" => "198387",
        "X-IG-App-ID" => "936619743392459",
        "X-IG-WWW-Claim" => "0",
        "X-Instagram-AJAX" => "3bcc4d0b0733",
        "X-Requested-With" => "XMLHttpRequest",
    ];

    const static_headers2 =  [
        "Accept" => "*/*",
        "Accept-Language" => "en-US,en;q=0.9,id;q=0.8",
        "Connection" => "keep-alive",
        "Host" => "i.instagram.com",
        "Origin" => "https://www.instagram.com",
        "Referer" => "https://www.instagram.com/",
        "Sec-Fetch-Dest" => "empty",
        "Sec-Fetch-Mode" => "cors",
        "Sec-Fetch-Site" => "same-origin",
        "X-ASBD-ID" => "198387",
        "X-IG-App-ID" => "936619743392459",
        "X-IG-WWW-Claim" => "0",
        "X-Instagram-AJAX" => "3bcc4d0b0733",
        "X-Requested-With" => "XMLHttpRequest",
    ];
}
