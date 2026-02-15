# Censo - Back-end
Área para cadastros, importações, manutenções e configurações do Pluri Connect.

## Executando via docker

### Criar uma imagem utilizando o script contido no Dockerfile
#### Linux
É necessário fazer a referência a uma chave ssh utilizada no bitbucket para conseguir baixar o projeto stack-dist
```shell
docker build -t censo-back-end .
```

#### Windows
```shell

```

### Cria um container a partir da imagem gerada e atrela ao terminal

#### Linux
```shell
docker run --name censo-back -it -v "$(pwd)":/var/www/html/ -p 8000:8000 censo-back-end
```

#### Windows
```shell
docker run --name censo-back -it -v "%cd%":/var/www/html/ censo-back-end
```

### Buscar o ip da instância que está em execução
```shell
docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' censo-back
```

### Instalando as dependências do projeto (após conectado ao container)
```shell
composer install
```

### Iniciando o serviço (após conectado ao container)
```shell
php -S 0.0.0.0:8000 -t public
```

### Conecta ao container que não está executando
```shell
docker start -a -i censo-back
```