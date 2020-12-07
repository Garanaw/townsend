<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Contact Form</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="/css/form.css" rel="stylesheet">
</head>
<body>
    <div id="app">
        <main class="">
            <form method="POST" action="/contact">
                <input type="hidden" name="_token" value="<?php echo \App\Foundation\Container::getInstance()->make('session')->token() ?>">
                <div>
                    <input type="text" name="name" placeholder="name" required>
                    <input type="tel" name="phone" placeholder="phone" required>
                    <input type="email" name="email" placeholder="email" required>
                    <textarea name="message" placeholder="message" required></textarea>
                </div>
                <button type="submit">Contact!</button>
            </form>
        </main>
    </div>
</body>
</html>

