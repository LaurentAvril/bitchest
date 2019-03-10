Installing project :
Copy and paste the .env.dist file, rename it in .env configuring it about line 16 :

DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
Normally on Windows, you can make something like that : DATABASE_URL=mysql://root:@127.0.0.1:3306/bitchest

Configuring Database :
In your favorite CLI, copy and paste the following code lines : Make sure database no exists

doctrine:database:drop --force
After that, create, migrate and load some fake fixtures

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
Running Server :
Make run your own local server pasting this code :

php bin/console server:run
