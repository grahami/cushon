# Cushon Tech Task - Ian Graham

## Introduction
This repository contains a sample application related to the purchase of an ISA by a retail customer. 

The scope of the application is the backend RESTful API and there are no front end components. The role of the front end is played by Postman in this exercise (see Postman below).

This approach was adopted so that the same solution could support both web, and native clients.

The solution is a fairly limited implementation and makes a number of assumptions and has comments about improvements and enhancements that have been left out.

## Use of Docker
Built on Docker with a docker-compose script to create two containers.
To run the solution locally
```
docker-compose build --no-cache
docker-compose up -d
```

### MySQL container
Represents a database server that is remote from the PHP code. This container creates the sample database, including tables and users and populates some sample data for retail customers and ISAs.

### Rest API container
Runs the PHP code, based on Symfony that presents a RESTful API for the sample application.

## The RESTful API
There are four routes (endpoints) in this solution. All accept and return data in JSON format. 

If an API call fails, an array is returned with an array of the errors. For this sample, both errors and valid data are returned with an HTTP response code of 200, rather than using HTTP error codes, such 406 (Not Acceptable).
```
{
    "errors": [
        "No matching retail customer found"
    ]
}
```

#### GET /api/retail_customer/{retail_customer_id}
Retrieves a single retail customer
```
{
    "data": {
        "id": 1,
        "firstName": "Ian",
        "lastName": "Graham",
        "emailAddress": "ian@igraham.me",
        "dob": "1990-01-01"
    }
}
```

#### GET /api/isas
Retrieves a JSON array of the available ISA's for the front end to display
```
{
    "data": [
        {
            "id": 1,
            "name": "Cushon Equities Fund",
            "type": "ISA",
            "riskDetails": "Medium risk, high return",
            "chargeDetails": "Platform charge 0.79%\nFund management 0.20%"
        },
        {
            "id": 2,
            "name": "Top Rated Ethical CushonMix",
            "type": "ISA",
            "riskDetails": "Medium risk, medium return",
            "chargeDetails": "Platform charge 0.79%\nFund management 0.21%"
        },
        {
            "id": 3,
            "name": "Top Rated CushonMix",
            "type": "ISA",
            "riskDetails": "Medium risk, good return",
            "chargeDetails": "Platform charge 0.79%\nFund management 0.22%"
        },
        {
            "id": 4,
            "name": "Junior ISA",
            "type": "JISA",
            "riskDetails": "Low Risk",
            "chargeDetails": "Platform charge 0.79%\nFund management 0.15%"
        },
        {
            "id": 5,
            "name": "Lifetime ISA",
            "type": "LISA",
            "riskDetails": "Low Risk, Government contribution matched 25%",
            "chargeDetails": "Platform charge 0.79%\nFund management 0.20%"
        }
    ]
}
```

#### POST /api/investment
Sends the customers selected ISA and amount to invest to API and returns details of the resulting investment

Payload
```
{
    "retail_customer_id": 1,
    "investments" : [
        {   "isa_id": 1,
            "lump_sum": 15000,
            "monthly_sum": 0
        }
    ]
}
```

Result
```
{
    "data": [
        {
            "id": 3,
            "retailCustomerId": 1,
            "isaId": 1,
            "investedAt": "2023-08-27 10:07:13",
            "lumpSum": 15000,
            "monthlySum": 0
        }
    ]
}
```
In the event that an invalid investment is requested, such as where the customer is not of an appropriate age, or the investment amount exceeds the Tax Free Allowance for the year, an appropriate element in the results array is returned.

For a future, fuller implementation, this would allow some investments to succeed, and other to fail, as well as providing an array structure for an overall result to the investment request.
```
{
    "data": [
        {
            "investment": 0,
            "errors": [
                "Customer is not eligible to invest in this ISA",
                "The investment amount exceeds the ISA tax free allowance"
            ]
        }
    ]
}
```


#### GET /report/retail-customer-investments/{retail_customer_id}
Retrieves a JSON array of all of the investments that a retail customer currently has
```
{
    "data": [
        {
            "id": 1,
            "first_name": "Ian",
            "last_name": "Graham",
            "dob": "1990-01-01",
            "email_address": "ian@igraham.me",
            "isa_name": "Cushon Equities Fund",
            "invested_at": "2023-08-27 10:07:13",
            "lump_sum": "15000.00"
        },
        {
            "id": 1,
            "first_name": "Ian",
            "last_name": "Graham",
            "dob": "1990-01-01",
            "email_address": "ian@igraham.me",
            "isa_name": "Top Rated Ethical CushonMix",
            "invested_at": "2023-08-27 10:07:53",
            "lump_sum": "2000.00"
        }
    ]
}
```
## Considerations and Notes 
This solution does not make use of an ORM, but rather implements the Repository Pattern. This allows for SQL queries to have a comment as part of the query, which identify the source of the query and can be helpful for debugging, support and mainteainance.

The SQL queries currently make use of `SELECT *` but a proper implementation would probably be best to specify exactly the fields required.

The repository pattern allows for the Report endpoint to retrieve a retail customer's investment report, and return a JSON array with the results, in a single query and with no Entity instantiation.

The requirement is for the retail customer functionality to be as separate from the employer based functionality as is reasonably possible. To support this, retail customers are stored in their own data repository and have they own, explicit classes and possibly even their own database.

By contrast, it might make more sense for the ISA's to be available to both retail and employer based customers, so the application could be modified so the details of ISA's are retrieved from a different database. If this approach was adopted then the current solution would require changes for the reporting functionality, which currently makes use of an SQL query that contains JOIN's to retrieve the complete customer investment report with a single query.

This sample application has some support for different types of ISA, e.g. normal ISA's, Junior ISA's and Lifetime ISA's, with appropriate validation for customer eligibility and checks for tax free allowances. 

When creating an investment, the API accepts an array of investment requests, but currently has validation that only allows a single investment at one time. In future, this restriction could be removed, and more validation would be required to ensure that the sum of all investments of appropriate types does not exceed the customer's Tax Free Allowance for the year.

There is also currently no handling for retrieving any existing investments a customer has for the current tax year, to avoid exceeding the tax free limit, or collection information from the customer about investments they may have with other providers.

For the report endpoint, there is currently no handling for pagination or filtering, such as by tax year.


## Room for improvement
There is currently no authorisation or authentication. In a full solution, there would typically be registration and login endpoints which would be used to identify the customer and to generate a JWT token. Given the use of a token, some of the API endpoints above could be modified and would no longer require the retail_customer_id.

The structure to handle multiple investments at one time has been put in place, but the validation does not currently support this.

Test coverage is currently very limited.



## Testing and validation


Static Analysis with PHPStan
```
cd /srv/cushon
vendor/bin/phpstan analyse src tests
```

Testing with PHPUnit
```
cd /srv/cushon
bin/phpunit
```

Postman

There is a Postman collection and matching environment file in the /postman directory of the project 