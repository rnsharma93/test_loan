## About

This project is just for learning purpose about REST API built on laravel. This is just simple Loan Application System where user can apply for loan and get the EMI amount, repayments on weekly basis.

## Minimum Requirements

- PHP 7.3
- MySql 5.x
- Apache 2.x

## Development Environments
- PHP 7.4
- MySql 5.7
- Apache 2.4

## Installation 

In any terminal/CMD follow the following steps

- git clone https://github.com/rnsharma93/test_loan.git
- cd test_loan
- composer install
- cp .env.example .env 
- php artisan migrate
- php artisan key:generate
- php artisan serve

update database credentials in .env file => DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

## Testing

- cp .env.example .env.testing 

* follow this command if you want to use different database for testing environments and update database credentials in .env.testing file 

* Test cases are written in tests directory, to run tests cases use following command

- php artisan test


## API endpoints

- /api/user/create              - Register User
- /api/user/login               - Login User
- /api/user                     - Get User Detail
- /api/loan/apply               - Apply for Loan
- /api/loan/{loan_id}/approve   - Approve Loan
- /api/loan/{loan_id}           - Get Loan detail and EMI repayments schedules 
- /api/loan/{loan_id}/pay       - Pay EMI payment 

Postman collection for API's
[https://documenter.getpostman.com/view/6195283/UVXjLbev] (https://documenter.getpostman.com/view/6195283/UVXjLbev)

Postman API collection has base url "http://localhost:8000" , update accordingly

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).