<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz Kontaktowy</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Skontaktuj się z nami</h1>
    </header>
    <main>
        <form id="contact-form" aria-labelledby="contact-form-title" novalidate>
            <fieldset>
                <legend id="contact-form-title">Formularz kontaktowy</legend>
                <div>
                    <label for="name">Imię i nazwisko <span aria-hidden="true">*</span></label>
                    <input type="text" id="name" name="name" required aria-required="true">
                </div>
                <div>
                    <label for="email">Adres e-mail <span aria-hidden="true">*</span></label>
                    <input type="email" id="email" name="email" required aria-required="true">
                </div>
                <div>
                    <label for="message">Wiadomość <span aria-hidden="true">*</span></label>
                    <textarea id="message" name="message" required aria-required="true"></textarea>
                </div>
                <div>
                    <button type="submit">Wyślij</button>
                </div>
            </fieldset>
            <div id="form-response" role="alert" aria-live="polite"></div>
        </form>
    </main>
    <script src="../js/form-validation.js"></script>
    <script src="../js/form-submit.js"></script>
</body>
</html>