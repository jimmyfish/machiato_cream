**DITO LAKSONO YUDHA PUTRA**

killcoder212@gmail.com

(+62) 8990314474

Challenge
---
This project is build with symfony skeleton version 4.3 (minimum installation) using design pattern concept for easy maintain for long term project.

```
Library list :
- symfony/orm-pack
- symfony/yaml
- symfony/http-client
- doctrine/annotations
- symfony/mailer
- symfony/test-pack
```

**Getting things ready**

Installing any dependency needed for project

` composer install `

Configuring database

1. Open your .env file
2. Change the configuration based on your database config  
    change line `DATABASE_URL=mysql://user:password@127.0.0.1:3306/dbname`
3. Change the configuration based on your mailer config, `MAILER_DSN=smtp://user:password@smtpurl:port?encryption=tls&auth_mode=login`
3. Run migration
    `php bin/console doctrine:schema:update --force`
4. Enjoy with a cup of coffee ~

**API Endpoint**

- Converting *.JSONL

    `data:migrate [options] [--] <src>`
    
    parameter :
    - `src` The source of file that being downloaded
    
    optional parameter :
    - `--filetype=FILETYPE` Specify the output filetype, (available options : `*.csv`, `*.yaml`) . default filetype is `*.csv`
    
    - `--db=DB` Save output into database (available options : `0, 1`) default option is `0`
    
    - `--email=EMAIL` Email result to specific email `example : &email=example@site.com`
    
    usage example :
    
    `php bin/console data:migrate https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl --filetype=csv` This will output the file with `*.csv` filetype
    
    `php bin/console data:migrate https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl --filetype=yaml` This will output the file with `*.yaml` filetype
    
    `php bin/console data:migrate https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl --filetype=yaml --email=dito@tuta.io`
    This will output the file with `*.yaml` filetype and save to database, and also will send to `dito@tuta.io` with `*.yaml` file attach.

    `data:migrate https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl --filetype=csv --db=true --email=dito@tuta.io`
    This will output the file with `*.csv` filetype and save to database, and also will send to `dito@tuta.io` with `*.csv` file attach