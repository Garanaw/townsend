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
    <style>
        .container {
            display: flex;
            width: 100%;
            max-width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
            justify-content: center;
        }

        label {
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .row {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            -ms-flex-preferred-size: 0;
            flex-basis: 0;
            -ms-flex-positive: 1;
            flex-grow: 1;
            max-width: 100%;
        }

        .form-control {
            display: block;
            width: 100%;
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .text-right {
            text-align: right !important;
        }
    </style>
</head>
<body>
    <div id="app" class="container">
        <header style="display: flex; justify-content: center; padding: 10px">
            <h2>Contact Form</h2>
        </header>
        <form method="POST" action="/contact">
            <input
                    type="hidden"
                    name="_token"
                    value="<?php echo \App\Foundation\Container::getInstance()->make('session')->token() ?>"
            >
            <div>
                <div class="row">
                    <div class="col text-right">
                        <label for="name">Name</label>
                    </div>
                    <div class="col">
                        <input type="text" id="name" class="form-control" name="name" placeholder="name" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-right">
                        <label for="phone">Phone Number</label>
                    </div>
                    <div class="col">
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="phone" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-right">
                        <label for="email">Email Address</label>
                    </div>
                    <div class="col">
                        <input type="email" id="email" class="form-control" name="email" placeholder="email" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-right">
                        <label for="message">Message</label>
                    </div>
                    <div class="col">
                        <textarea id="message" class="form-control" name="message" placeholder="message" required></textarea>
                    </div>
                </div>
            </div>
            <button type="submit">Contact!</button>
        </form>
    </div>
</body>
</html>

