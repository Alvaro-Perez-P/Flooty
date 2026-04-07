<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<style>
    :root {
        --verde-hielo: #F7FEEF;
        --verde-suave: #CBDDB5;
        --verde-natural: #A8CA7E;
        --verde-organico: #97B770;
        --verde-profundo: rgb(96, 131, 52);
    }

    .cubos {
        display: flex;
        flex-direction: row;
        margin: 20px;
        padding: 20px;
    }

    .cubo-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: auto;
    }

    .cubo {
        width: 100px;
        height: 100px;
        border: solid 1px black;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .nombre-variable {
        margin-top: 8px;
        font-size: 12px;
        font-family: monospace;
    }

    .cuboa {
        background-color: var(--verde-hielo);
    }

    .cubob {
        background-color: var(--verde-suave);
    }

    .cuboc {
        background-color: var(--verde-natural);
    }

    .cubod {
        background-color: var(--verde-organico);
    }

    .cuboe {
        background-color: var(--verde-profundo);
    }
</style>

<body>

    <h1>Panton de colores</h1>
    <div class="cubos">

        <div class="cubo-container">
            <div class="cubo cuboa">Verde hielo</div>
            <div class="nombre-variable">--verde-hielo</div>
        </div>

        <div class="cubo-container">
            <div class="cubo cubob">Verde suave</div>
            <div class="nombre-variable">--verde-suave</div>
        </div>

        <div class="cubo-container">
            <div class="cubo cuboc">Verde Natural</div>
            <div class="nombre-variable">--verde-natural</div>
        </div>

        <div class="cubo-container">
            <div class="cubo cubod">Verde orgánico</div>
            <div class="nombre-variable">--verde-organico</div>
        </div>

        <div class="cubo-container">
            <div class="cubo cuboe">Verde profundo</div>
            <div class="nombre-variable">--verde-profundo</div>
        </div>

    </div>

</body>

</html>