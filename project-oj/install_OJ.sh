#!/bin/bash
#defore run this scripte please install the lnmp
#before install check DB setting in 
#	judge.conf 
#	hustoj-read-only/web/include/db_info.inc.php
#	and down here
#and run this with root
# install for lnpm linux nginx php mysql
#CENTOS/REDHAT/FEDORA WEBBASE=/var/www/html APACHEUSER=apache 
#lnmp 
WEBBASE=/home/wwwroot/default/
APACHEUSER=www
DBUSER=root
DBPASS=root

#try install tools
sudo apt-get install flex g++ libmysql++-dev  php5-mysql php5-gd php5-cli mono-gmcs subversion
sudo /etc/init.d/mysql start

sudo yum -y update
sudo yum -y install  php-xml php-gd gcc-c++  mysql-devel php-mbstring glibc-static
sudo /etc/init.d/mysqld start

sudo svn checkout http://hustoj.googlecode.com/svn/trunk/ hustoj-read-only

#create user and homedir
sudo  /usr/sbin/useradd -m -u 1536 judge



#compile and install the core
cd hustoj-read-only/core/
sudo ./make.sh
cd ../..
#install web and db
sudo cp -R hustoj-read-only/web $WEBBASE/JudgeOnline
sudo chmod -R 771 $WEBBASE/JudgeOnline
sudo chown -R $APACHEUSER $WEBBASE/JudgeOnline
sudo mysql -h localhost -u$DBUSER -p$DBPASS < db.sql

#create work dir set default conf
sudo    mkdir /home/judge
sudo    mkdir /home/judge/etc
sudo    mkdir /home/judge/data
sudo    mkdir /home/judge/log
sudo    mkdir /home/judge/run0
sudo    mkdir /home/judge/run1
sudo    mkdir /home/judge/run2
sudo    mkdir /home/judge/run3
sudo cp java0.policy  judge.conf /home/judge/etc
sudo chown -R judge /home/judge
sudo chgrp -R $APACHEUSER /home/judge/data
sudo chgrp -R root /home/judge/etc /home/judge/run?
sudo chmod 775 /home/judge /home/judge/data /home/judge/etc /home/judge/run?

#boot up judged
sudo cp judged /etc/init.d/judged
sudo chmod +x  /etc/init.d/judged
sudo ln -s /etc/init.d/judged /etc/rc3.d/S93judged
sudo ln -s /etc/init.d/judged /etc/rc2.d/S93judged
sudo /etc/init.d/judged start
sudo /etc/init.d/nginx restart
#sudo /etc/init.d/nginx restart

