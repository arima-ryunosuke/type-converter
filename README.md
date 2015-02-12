Type Converter
====

## Description

php の配列と、xml, yml, json, csv などの相互変換を行います。

## Demo

下記のような xml は

```xml
<root>
    <holder>
        <empty></empty>
    </holder>
    <holder>
        <values>
            <key1>value</key1>
            <key2>value</key2>
        </values>
    </holder>
    <holder>
        <values attr1="attrvalue1" attr2="attrvalue2">
            <key1>value1</key1>
            <key2>value2</key2>
        </values>
    </holder>
</root>
```

下記のような json に変換されます。

```json
{
    "holder": [
        {
            "empty": ""
        },
        {
            "values": {
                "key1": "value",
                "key2": "value"
            }
        },
        {
            "values": {
                "@attributes": {
                    "attr1": "attrvalue1",
                    "attr2": "attrvalue2"
                },
                "key1": "value1",
                "key2": "value2"
            }
        }
    ]
}
```

## Usage

各クラスは自身の形式と php のネイティブ配列との変換のみを行います。
xml -> json のような変換を行う場合は、各クラスの `convert`, `deconvert` を織り交ぜるか、 `Mediator` クラスを使って下さい。

## Install

```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/arima-ryunosuke/type-converter"
        }
    ],
    "require": {
        "ryunosuke/type-converter": "dev-master"
    }
}
```

```bash
$ cd project_root
$ composer install
```

yaml の変換は php の拡張が必要です。

## Licence

[MIT](https://raw.githubusercontent.com/arima-ryunosuke/type-converter/master/LICENSE)

## Author

[arima-ryunosuke](https://github.com/arima-ryunosuke)
