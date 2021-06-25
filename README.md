## Solution

- I tried to keep it simple by streaming input data and chuncking before inserting into DB
and by doing that inside a transaction will make sure that all data either will be inserted or all is failed. when it fails it's run inside a queue job. so it could be tried again serveral times.

- Added a simple adapter implementation in case we have another client that sends another data formats.

## possipole improvments 

- If we have larger set's of data we could split them into multiple jobs and batches them, or we could also split the main file into smaller files and work on each one of them individually.

- we could also user strategy or repository to exchange between data sources xsv / json / Excel ...etc

## Setup

Either used artisan serve or sail you have to run

- `php artisan migrate`
- I've created endpoint for running the script http://127.0.0.1:8000/process-file