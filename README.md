1. docker-compose up -d
2. composer install
3. wget https://github.com/fabpot/local-php-security-checker/releases/download/v2.0.6/local-php-security-checker_2.0.6_linux_amd64
4. mv local-php-security-checker_1.2.0_linux_amd64 local-php-security-checker
5. chmod +x local-php-security-checker
6. ./local-php-security-checker
7. mysql -u root -proot -e "USE snw" -e "GRANT ALL PRIVILEGES ON snw to 'username'@'%'"
8. php bin/console doctrine:migrations:migrate
9. php bin/console doctrine:fixtures:load
10. npm install
11. npm run watch
12. docker run --rm -ti -p 8025:8025 -p 1025:1025 --network=social-news-website_main mailhog/mailhog
