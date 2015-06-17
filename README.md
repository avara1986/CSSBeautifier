# CSS Beautifier
[![Build Status](https://travis-ci.org/avara1986/CSSBeautifier.svg?branch=master)](https://travis-ci.org/avara1986/CSSBeautifier)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2a59ada0-9b0f-4ebc-874b-5f756a852fc1/mini.png)](https://insight.sensiolabs.com/projects/2a59ada0-9b0f-4ebc-874b-5f756a852fc1)

## Proyectos:
**REST API**

[github.com/avara1986/CSSBeautifier](https://github.com/avara1986/CSSBeautifier)

**Front-End**

[github.com/josex2r/CSSBeautifier-Front](https://github.com/josex2r/CSSBeautifier-Front)

## Uso de la API

### 1. Guardar una web:
Llamar a la siguiente API por POST. pasando la variable webiste
```
http://api.cssbeautifier.com/api/website/
```
Con Curl:
```sh
$ curl -X POST --data "website=http://www.we-ma.com/" http://api.cssbeautifier.com/api/website/
```
Devolverá un JSON con el Identificador y un token para continuar los procesos:
```sh
{"id":12,"token":"wDWGRgXEJ5qlI9IaJmV9cL/a3+o="}
```
### 2. Recuperar CSS una web:
Llamar a la siguiente API por GET. pasando en la URL el ID y el TOKEN
```
http://api.cssbeautifier.com/api/website/[ID]/[TOKEN]
http://api.cssbeautifier.com/api/website/[ID]?token=[TOKEN]
```
Siguiendo el ejemplo anterior, sería:
```sh
http://api.cssbeautifier.com/api/website/12?token=wDWGRgXEJ5qlI9IaJmV9cL/a3+o=
http://api.cssbeautifier.com/api/website/12/wDWGRgXEJ5qlI9IaJmV9cL/a3+o=
```
o

Devolviendo un JSON Con todos los CSS y otra vez los identificadores de la web. Por ejemplo:
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
Ahora. Con estos CSS, podremos actualizar sus datos de cada uno llamando a la siguiente API pasando el ID del Csv y el Token de su web:
```
http://api.cssbeautifier.com/api/css/
```
Con Curl:
```sh
$ curl -X POST --data "id=21&token=wDWGRgXEJ5qlI9IaJmV9cL%2Fa3%2Bo%3D" http://api.cssbeautifier.com/api/css/
```
Devolverá un código de respuesta 200 si todo OK. 404 si no encontró el CSS.

Para recuperar los datos, los CSS comprimidos y el CSS limpio se llamará a la API por GET:

```sh
http://api.cssbeautifier.com/api/css/21?token=wDWGRgXEJ5qlI9IaJmV9cL/a3+o=
http://api.cssbeautifier.com/api/css/21/wDWGRgXEJ5qlI9IaJmV9cL/a3+o=
```
Devolviendo un JSON. Por ejemplo

```sh
{"id":12,
"created":"2015-05-28T12:34:44+0200",
"original":"@charset ....",
"original_compressed":"@charset ....",
"beauty":"@charset ....",
"beauty_compressed":"@charset ....",
}
```