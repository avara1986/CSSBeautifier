# CSS Beautifier
[![Build Status](https://travis-ci.org/avara1986/CSSBeautifier.svg?branch=master)](https://travis-ci.org/avara1986/CSSBeautifier)
[![Coverage Status](https://coveralls.io/repos/avara1986/CSSBeautifier/badge.svg)](https://coveralls.io/r/avara1986/CSSBeautifier)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2a59ada0-9b0f-4ebc-874b-5f756a852fc1/mini.png)](https://insight.sensiolabs.com/projects/2a59ada0-9b0f-4ebc-874b-5f756a852fc1)

## Projects:
**REST API**

[github.com/avara1986/CSSBeautifier](https://github.com/avara1986/CSSBeautifier)

**Front-End**

[github.com/josex2r/CSSBeautifier-Front](https://github.com/josex2r/CSSBeautifier-Front)

## API

### 1. Save a website:
make calls to the API with POST method. Send "webiste" param 
```
http://api.cssbeautifier.com/api/website/
```
Curl:
```sh
$ curl -X POST --data "website=http://www.we-ma.com/" http://api.cssbeautifier.com/api/website/
```
It will return JSON with identifier "id" and a token. We will use it in nexts API calls:
```sh
{"id":12,"token":"wDWGRgXEJ5qlI9IaJmV9cL/a3+o="}
```
### 2. Get Website CSS's:
Call to the API with GET method. Send "id" and "token" param.
```
http://api.cssbeautifier.com/api/website/[ID]/[TOKEN]
http://api.cssbeautifier.com/api/website/[ID]?token=[TOKEN]
```
For example:
```sh
http://api.cssbeautifier.com/api/website/12?token=wDWGRgXEJ5qlI9IaJmV9cL/a3+o=
http://api.cssbeautifier.com/api/website/12/wDWGRgXEJ5qlI9IaJmV9cL/a3+o=
```
It will return a JSON with all website's CSS.

```sh
{"id":12,
"token":"wDWGRgXEJ5qlI9IaJmV9cL/a3+o=",
"css":[{
    "id":18,"url":"podcast\/css\/bootstrap.min.css"},
    {"id":19,"url":"podcast\/css\/bootstrap-theme.min.css"},
    {"id":20,"url":"podcast\/css\/font-awesome.min.css"},
    {"id":21,"url":"podcast\/css\/style.css"},{"id":22}
    ]}
```
With this CSS's Ids, we can use the CSS APIS.

We call the next API with POST Method with params CSS ID and Website Token:
```
http://api.cssbeautifier.com/api/css/
```
Curl:
```sh
$ curl -X POST --data "id=21&token=wDWGRgXEJ5qlI9IaJmV9cL%2Fa3%2Bo%3D" http://api.cssbeautifier.com/api/css/
```

This API search and generates the new CSS file, compressed and beautifier. It will return status code 200 if all Ok and 404 if it can't find or update the CSS

To CSS file compressed we call this API with GET Method with params CSS ID and Website Token:

```sh
http://api.cssbeautifier.com/api/css/21?token=wDWGRgXEJ5qlI9IaJmV9cL/a3+o=
http://api.cssbeautifier.com/api/css/21/wDWGRgXEJ5qlI9IaJmV9cL/a3+o=
```
The API will return a JSON like this:

```sh
{"id":12,
"created":"2015-05-28T12:34:44+0200",
"original":"@charset ....",
"original_compressed":"@charset ....",
"beauty":"@charset ....",
"beauty_compressed":"@charset ....",
}
```
