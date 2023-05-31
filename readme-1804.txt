-- UBUNTU 18.04 ONLY --

###########################################################################################
# THIS INSTALL IS BASED ON INSTALLING ON FILES IN PANEL FOLDER TO THE ROOT OF HTML FOLDER #
###########################################################################################

--REQUIREMENTS
sudo apt -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt full-upgrade
sudo apt install mysql-server apache2 aria2 -y
sudo apt-get install -y php7.4 php7.4-cli php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmath php7.4-bz2 php7.4-xmlrpc

--DOWNLOAD
cd /home
sudo apt install git
git clone https://github.com/DRM-Scripts/DRMPHP

--SETUP
sudo nano /etc/mysql/my.cnf - Copy the below into the cnf file
[mysqld]
sql-mode="NO_ENGINE_SUBSTITUTION"

sudo service mysql restart
cd DRMPHP/
sudo cp -r cli /etc/php/7.4/
sudo nano /etc/php/7.4/apache2/php.ini - Enable Short Open Tags

sudo visudo - Copy the below into visudo
www-data ALL=(ALL) NOPASSWD: ALL

sudo service apache2 restart
cd /home
sudo wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz
sudo tar -xf ffmpeg-release-amd64-static.tar.xz
sudo cp -r ffmpeg-6.0-amd64-static/* /usr/bin
cd DRM*
sudo cp -r panel/. /var/www/html
cd /var/www/html
sudo chmod +x mp4decrypt
sudo mkdir download
sudo chmod 777 download
cd ../
mkdir backup
sudo chmod 777 backup
sudo chmod 777 html
cd /home
cd DRM*
sudo cp panel/downloader.php /var/www/html
cd /home/DRMPHP
sudo chmod 777 ./db.sh
sudo sed -i -e 's/\r$//' db.sh
sudo ./db.sh - Fill in the MYSQL Database & User Details
mysql -u root -p
USE DBNAME
SET GLOBAL sql_mode = 'NO_ENGINE_SUBSTITUTION';
SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';
exit
cd /var/www/html
sudo nano _db.php - Enter Your DB & User Details
sudo service apache2 restart

--ACCESS
browse to pub server ip
login with admin/Admin@2023##
