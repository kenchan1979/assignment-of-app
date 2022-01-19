#assignment of app

#Name

Name is 'みんなのプラン'.

#Demo

#Features

You, family, friends and colleagues can make plans easily.
You can share plans with those people.
And also you can comment on plans.

#Requirement

Windows
macOS

If you can use web browser like google chrome,it's available.

#Installation

Execute following command.
In Windows's case, on command prompt.
In macOS's case, on terminal.

```bush
git clone git@github.com:kenchan1979/assignment-of-app.git
docker-coompse up -d
```

Creating database.

```bush
CREATE DATABASE pralearn;
```

Creating user and granting privileges.

```bush
CREATE USER teuser IDENTIFIED BY 'pwd';
GRANT ALL ON pralearn.* TO teuser;
```

Settings database.

```bush
docker-compose exec app php ./db/db_setup.php
データベースをSET UPしますか？ [yes] or [no]
```
If '===データベース set up完了===' appears, settings database completes!

#Usage

First of all, you can make account from 'アカウント登録'.
And then you can make plans and comment on any plans after login.
Even if you don't have own account or don't login, you can see any plans.

#Auther

Name: ken who belong to Elites.inc(https://elites.education/) as student
Email: kensuga0215@gmail.com

#License

No license.
Anybody can use.



