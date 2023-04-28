-- UBUNTU 18.04 ONLY --

--REQUIREMENTS
sudo apt -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt full-upgrade
sudo apt install mysql-server apache2 aria2 -y
sudo apt-get install -y php7.4 php7.4-cli php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmath php7.4-bz2 php7.4-xmlrpc

--DOWNLOAD
cd /home
apt install git
git clone https://github.com/DRM-Scripts/DRMUniversal

--SETUP
nano /etc/mysql/my.cnf - Copy the below into the cnf file
[mysqld]
sql-mode="NO_ENGINE_SUBSTITUTION"

service mysql restart
cd DRMUniversal/
cp -r cli /etc/php/7.4/
nano /etc/php/7.4/apache2/php.ini - Enable Short Open Tags

visudo - Copy the below into visudo
www-data ALL=(ALL) NOPASSWD: ALL

service apache2 restart
cd /home
wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz
tar -xf ffmpeg-release-amd64-static.tar.xz
cp -r ffmpeg-6.0-amd64-static/* /usr/bin
cd /var/www/html
chmod +x mp4decrypt
mkdir download
chmod 777 download
cd ../
mkdir backup
chmod 777 backup
chmod 777 html
cd /home
cd DRM*
cp downloader.php /var/www/html
cp panel/downloader.php /var/www/html
cd /home/DRMUniversal
chmod 777 ./db.sh
sed -i -e 's/\r$//' db.sh
./db.sh - Fill in the MYSQL Database & User Details
cd /var/www/html
nano _db.php - Enter Your DB & User Details
service apache2 restart

--ACCESS
browse to pub server ip
login with admin/Admin@2022##
