<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió de Matrícules</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: white;
            background-color: #000;
            position: relative;
            overflow: hidden;
        }

        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
            opacity: 0.4;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5));
            z-index: -1;
        }

        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 20px;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 1px 1px 10px rgba(0, 0, 0, 0.7);
        }

        p {
            font-size: 1.2rem;
            max-width: 800px;
            line-height: 1.6;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 8px rgba(0, 0, 0, 0.6);
        }

        .cta-button {
            padding: 12px 25px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }

        .cta-button:hover {
            background-color: #0056b3;
            box-shadow: 0 6px 15px rgba(0, 86, 179, 0.5);
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }

            p {
                font-size: 1rem;
            }

            .cta-button {
                font-size: 1rem;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

    <img class="background-image" src="{{ asset('storage/barriVellBny.png') }}" alt="Imatge de fons">
    <div class="overlay"></div>

    <div class="content">
        <h1>Pàgina de gestió de matrícules de Banyoles</h1>
        <p>Aquesta pàgina web permet gestionar les matrícules dels vehicles del barri vell de Banyoles.</p>
        <a href="/admin" class="cta-button">Accés</a>
    </div>

</body>
</html>
