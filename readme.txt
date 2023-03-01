--REQUIREMENTS
sudo apt -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo add-apt-repository ppa:savoury1/ffmpeg5
sudo add-apt-repository ppa:savoury1/ffmpeg4
sudo apt update
sudo apt full-upgrade
sudo apt install ffmpeg mysql-server aria2 -y
sudo apt-get install -y php7.4 php7.4-cli php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmath

--SETUP
create MySQL database
create MySQL user & grant permissions
import db.sql to database
set MySQL to NO_ENGINE_SUBSTITUTION
copy files from cli to etc/php/7.4/cli
enable short_open_tags in etc/php/7.4/apache
edit _db.php with MySQL details
copy panel files to var/www/html
Edit visudo (sudo visudo)
www-data ALL=(ALL) NOPASSWD: ALL
restart apache service

--ACCESS
browse to pub server ip
login with admin/Admin@2022##