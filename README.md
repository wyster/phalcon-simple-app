**Запуск контейнера**

1. Сборка командой
 
`$ docker image build -t phalcon-simple-app .`

2. Запуск 

`$ docker container run --env USE_PHP_INTERNAL_SERVER=1 --publish 80:80 -v $(pwd):/var/www/html phalcon-simple-app`

В `--publish` можно передать желаемый порт, например 8080 `--publish 8080:80`

С флагом `-d` можно запустить в `detached mode`

3. Сайт доступен на выбранном вами порту, по умолчанию на 80: `http://localhost/`

**Тесты**

```
$ make unit-test
```

**Покрытие**

```
$ make coverage
```

