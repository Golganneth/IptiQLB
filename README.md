# IptiQLB
Technical assesment for IptiQ

## How to install?

1. Make sure PHP (>= 7.2) is installed in the system.
2. Install composer (https://getcomposer.org/download/)
3. Clone the repository
4. Access the project folder 
5. Run composer install
6. Run the unit tests from the project root with the command ./vendor/bin/phpunit test/

## Architecture
The system tries to follow DDD principles to provide clear separation between application and domain logic. The main folders are:
- Application: Contains an application service (LoadBalancer).
- Domain: Contains the entities (mainly Provider), domain services
- Infrastructure: Contains the concrete implementation of some domain services.

The solution has a lot of improvement points. Given the 4 main components (Load Balancer, Pool, HeartBeatChecker and CapacityControl), it wuold be possible to take an event-driven approach, where each one of the components would publish what's happening on them to an Event Bus and react to the events they are interested in. A simple case would be the capacity control when a new provider is added to a pool or when it's included and excluded. Another example is the outcome of the heartbeat checker publishing the result of each check to the event bus for the health control process to handle what's going on, separting concerns even more.

Given the time, the approach is much more simpler. 

