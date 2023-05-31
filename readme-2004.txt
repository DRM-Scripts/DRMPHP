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
10. cd DRM*
11. cp -r cli /etc/php/7.4
12. wget http://ftp.de.debian.org/debian/pool/main/n/nghttp2/libnghttp2-14_1.52.0-1_amd64.deb
13. dpkg -i libnghttp2-14_1.52.0-1_amd64.deb
14. edit /etc/php/7.4/apache2/php.ini using nano -> ctrl + w -> short_open_tag -> change short_open_tag = Off to be short_open_tag = On 
15. visudo -> add www-data ALL=(ALL) NOPASSWD: ALL in the most bottom
16. service apache2 restart
17. cd /home
18. wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz
19. tar -xf ffmpeg-release-amd64-static.tar.xz
20. cp -r ffmpeg-6.0-amd64-static/* /usr/bin
21. cd DRM*
22. cp -r panel/. /var/www/html
23. cd /var/www/html
24. chmod +x mp4decrypt
25. mkdir download
26. chmod 777 download
27. cd ../
28. mkdir backup
29. chmod 777 backup
30. chmod 777 html
31. cd /home
32. cd DRM*
34. cp downloader.php /var/www/html
35. cp panel/downloader.php /var/www/html
36. cd /home/DRM*
37. chmod 777 ./db.sh
38. sed -i -e 's/\r$//' db.sh
39. ./db.sh - Fill in the MYSQL Database & User Details
40. cd /var/www/html
41. nano _db.php - Enter Your DB & User Details
42. service apache2 restart
43. browse to pub server ip
44. login with admin/Admin@2023##
