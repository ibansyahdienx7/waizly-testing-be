<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Iban Syahdien Akbar

Hi, Introduce me Iban Syahdan Akbar.

I graduated from Gunadarma University, I majored in information systems, I learned about website-based programming and how to create structures that are used. Therefore, I am very interested in learning more about designing and building so that I can find out what is needed by a web-based application sequentially. 

To solve a problem on such a web-based application, I have experience in creating and designing through several projects, all done by defining various stakeholder requirements, conducting interviews with users so I can get their insights, learn from them, and turn them into ideas. which will provide a better experience for them. 

Apart from learning about Website-Based Programming, I don't limit myself to learning other things related to my major such as analysis and design of information systems and database systems. 

I can handle working with different types of people to create new experiences and learn new perspectives, I can work in a team or individually, I also want to learn more about the work I will be working on in the future.

## Follow Social Media

- [Instagram](https://www.instagram.com/ibansyah_/).
- [LinkedIn](https://www.linkedin.com/in/ibansyahdien/).

## Run Program

- Create .env file from .env.example
- After that, Copy Paste in the .env.example file into the .env that was created
- Create databases
- Configure the database in the .env file
- After that run "php artisan migrate" to migrate the database
- After the migrate is successful, run the program by "php artisan serve" you can customize the port on your php artisan serve, like the example in .env.example. The file is listed as http://127.0.0.1:3199, for 3199 is a port where the default port of php artisan is port 8000.
- If you want to change the port as above or you want to customize it to your liking, do it in the following way: php artisan serve --port={according to your wishes}
Example: php artisan serve --port=3199
- If you have done a custom port on php artisan things you should pay attention to in the .env file and the config/app file.
In the .env file change it to a url with your custom port, for example: "APP_URL=http://127.0.0.1:3199"
And in the config/app file, change it with the following example: `'url' => env('APP_URL', 'http://127.0.0.1:3199')`
- So everything has run successfully
- Because this project is a Back End Developer project to do a trial, please check postman to do a trial program.
- Then, check in a routes/api. The file provides an end point for running the program.
- After that, check one of the end points in postman, like the following example: http://127.0.0.1:3199/api/auth/list
- Then the program is successfully executed, the success/failure response will be listed below in the postman
- That's all the way to run this project, if you have trouble, please contact email: ibansyahdienx7@gmail.com

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
