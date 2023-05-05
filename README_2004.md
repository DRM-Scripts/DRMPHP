# Ubuntu 20.04
1. apt update
2. sudo apt -y install software-properties-common
3. sudo add-apt-repository ppa:ondrej/php
4. sudo add-apt-repository ppa:savoury1/curl34
5. apt update
6. sudo apt install mysql-server apache2 aria2 -y
7. sudo apt-get install -y php7.4 php7.4-cli php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmath php7.4-bz2 php7.4-xmlrpc
8. apt install git
9. git clone this repos at /home
10. cd DRMUniversal
11. cp -r cli /etc/php/7.4
12. edit /etc/php/7.4/apache/php.ini using nano -> ctrl + w -> short_open_tag -> change short_open_tag = Off to be short_open_tag = On 
13. visudo -> add www-data ALL=(ALL) NOPASSWD: ALL in the most bottom
14. service apache2 restart
15. cd /home
wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz
tar -xf ffmpeg-release-amd64-static.tar.xz
cp -r ffmpeg-6.0-amd64-static/* /usr/bin
cd DRM*
cp -r panel/. /var/www/html
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
