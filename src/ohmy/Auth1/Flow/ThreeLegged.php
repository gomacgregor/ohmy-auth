<?php namespace ohmy\Auth1\Flow;

/*
 * Copyright (c) 2014, Yahoo! Inc. All rights reserved.
 * Copyrights licensed under the New BSD License.
 * See the accompanying LICENSE file for terms.
 */

use ohmy\Auth\Flow,
    ohmy\Auth1\Security\Signature,
    ohmy\Auth1\Flow\ThreeLegged\Request;

class ThreeLegged extends Flow {

    private $client;

    public function __construct($callback, $client=null) {
        parent::__construct($callback);
        $this->client = ($client) ?  $client : new Client;
    }

    public function request($url, $options) {
        $promise = $this;
        return (new Request(function($resolve, $reject) use($promise, $url, $options) {

            if ($promise->value['oauth_token']) {
                $resolve($promise->value);
                return;
            }

            // sign request
            /*
            $request = new SignedRequest(
                ($options['method']) ? $options['method'] : 'POST',
                $url,
                array_intersect_key(
                    $promise->value,
                    array_flip(array(
                        'oauth_callback',
                        'oauth_consumer_key',
                        'oauth_consumer_secret',
                        'oauth_timestamp',
                        'oauth_nonce',
                        'oauth_signature_method',
                        'oauth_version'
                    ))
                )
            );

            $promise->client->enqueue($request, function($response) use($promise, $resolve, $reject) {
                if ($response->getResponseCode() === 200) {
                    # return a string
                    echo '<pre>';
                    var_dump($response->getBody()->toString());
                    echo '</pre>';
                    $resolve($response->getBody()->toString());
                }
            });

            # send request
            $promise->client->send();
            */

        }, $this->client))

    ->then(function($data) use($promise) {
            if (is_array($data)) return $data;
            parse_str($data, $array);
            $_SESSION['oauth_token_secret'] = $array['oauth_token_secret'];
            return array_merge($promise->value, $array);
        });
    }

}
