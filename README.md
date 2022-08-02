## Task

## Installation

1. git clone ...
2. composer install
3. update DATABASE_URL in .env to connect to local db
4. php bin/console make:migration
5. php bin/console doctrine:migrations:migrate
6. symfony server:start
7. go to http://127.0.0.1:8000

## Tables and relations

- User
  - name
- Swipes
  - userA - ManyToOne - user->getSwipes()
  - userB - ManyToOne
  - action - boolean
- Pairs
  - userA - ManyToOne - user->getPairs()
  - userB - ManyToOne