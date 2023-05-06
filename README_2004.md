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
16. wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz
17. tar -xf ffmpeg-release-amd64-static.tar.xz
18. cp -r ffmpeg-6.0-amd64-static/* /usr/bin
19. cd DRM*
20. cp -r panel/. /var/www/html
21. cd /var/www/html
22. chmod +x mp4decrypt
23. mkdir download
24. chmod 777 download
25. cd ../
26. mkdir backup
27. chmod 777 backup
28. chmod 777 html
29. cd /home
30. cd DRM*
31. cp downloader.php /var/www/html
32. cp panel/downloader.php /var/www/html
33. cd /home/DRMUniversal
34. chmod 777 ./db.sh
35. sed -i -e 's/\r$//' db.sh
36. ./db.sh - Fill in the MYSQL Database & User Details
37. cd /var/www/html
38. nano _db.php - Enter Your DB & User Details
39. service apache2 restart
40. browse to pub server ip
41. login with admin/Admin@2022##
