<?php
require_once("../../actions/usuario/identifyUsuarioLogado.php");

// se não tiver logado, vai para o login
if (!isset($_SESSION['login'])) {
    header("Location: ../usuario/login.php?");
    exit();
}?>

<?php
require_once("../base/popups/popup.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../../public/images/Logo.png">
    
    <title>VILIBRAS</title>
    <link rel="stylesheet" href="../../../public/css/aula-style.css">
    <link rel="stylesheet" href="../../../public/css/aula-portrait-style.css" media="screen and (orientation: portrait)">
    <link rel="stylesheet" href="../../../public/css/aula-landscape-style.css" media="screen and (orientation: landscape)">


</head>
<body>

<header>
    <div class="menu-icon fa-solid fa-bars-staggered" id="menu-toggle"></div>
    <div class="logo"><img src="../../../public/images/logo.png" alt="Logo"><p><a href="../dashboard/dashboard.php">VILIBRAS</a></p></div>
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Pesquisar">
        <button class="fa-solid fa-magnifying-glass"></button>
        <div id="search-results" class="search-results"></div>
    </div>
    <div class="user-options">
        <button id="theme-toggle">🌙</button>
        <button id="user-button"><a href="../perfil/perfil.php"><i class="fas fa-user user-icon"></i></a></button>
    </div>
</header>

<div class="container">
    <div class="objects-3d">
        <img class="element1" src="../../../public/images/objetos 3d.png" alt="">
        <img class="element2" src="../../../public/images/objetos 3d.png" alt="">

    </div>
    <aside id="sidebarMenu">
        <button id="close-sidebar" class="close-sidebar-button">&#10005;</button>
        <nav>
            <ul>
                <hr>
                <div class="btnsNav">
                    <button class="sign-in-button"><a href="../dicionario/dicionario.php">📖Dicionario</a></button>
                    <button class="sign-in-button"><a href="../questoes/questoes.php">❓ Questões</a></button>
                    <button class="sign-in-button"><a href="../dashboard/dashboard.php">🏠 Home</a></button>
                </div>
                <hr>
                <?php
                require_once("../../config/conecta.php");
                conecta();
                global $mysqli;

                $sql = "SELECT id, titulo, descricao, iframe FROM aulas";
                $result = $mysqli->query($sql);
                $aulas = [];
                while ($row = $result->fetch_assoc()) {
                    $aulas[] = $row;
                }
                desconecta();

                $userName = '';
                if (isset($usuarioLogado['nome'])) {
                    $userName = $usuarioLogado['nome'];
                }
                ?>
                <br> <br>

                <?php if (!empty($aulas)): ?>
                    <?php foreach ($aulas as $index => $aula): ?>
                        <li data-content="content<?php echo $index + 1; ?>">📼 <?php echo htmlspecialchars($aula['titulo']); ?></li>
                        <?php if (($index + 1) % 4 === 0): ?>
                            <hr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Nenhuma aula encontrada.</li>
                <?php endif; ?>
                <br> <br>

                <hr>
                <li><strong>Ajuda</strong></li>
                <div class="help-options">
                    <button><i class="fas fa-cog config-icon"></i></button>
                    <button><i class="fa-solid fa-info"></i></button>
                </div>
            </ul>
        </nav>
    </aside>
    <main>
        <div class="hero">
            <div class="main-intro">
                <!-- <div class="logomain">
                    <img src="../../../public/images/Logo.png" alt=""> -->
                </div>

                <p id="text-intro">Olá <span><?php echo htmlspecialchars($userName);?>,</span><br><br>Seja bem vindo(a)<br>a sessão de vídeos da <br>plataforma <em><span>VILIBRAS</span></em></p>
                <img class="blocks-img" src="../../../public/images/cubos-3d-abstratos.png" alt="">
            </div>
            <img class="alert-icon" src="../../../public/images/help-web-button_18436.png" alt="">

            <p>Selecione uma aula para assistir!</p>
            <div class="wrapper">
                <div class="box a">
                    <div class="skeleton-box"></div>
                </div>
                <div class="box b">
                    <div class="skeleton-box"></div>
                </div>
                <div class="box c">
                    <div class="skeleton-box"></div>
                </div>
                <div class="box d">
                    <div class="skeleton-box"></div>
                </div>
                <div class="box e">
                    <div class="skeleton-box"></div>
                </div>
            </div>
        </div>
    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('search-input').addEventListener('input', function () {
        const query = this.value;
        if (query.length > 2) {
            fetch(`search.php?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    const resultsContainer = document.getElementById('search-results');
                    resultsContainer.innerHTML = '';
                    data.forEach(aula => {
                        const resultItem = document.createElement('div');
                        resultItem.classList.add('result-item');
                        resultItem.innerHTML = `<div class="video-search"><strong>${aula.titulo}</strong><p>${aula.descricao}</p></div><div class="video-box"></div>` ;
                        resultsContainer.appendChild(resultItem);
                    });
                });
        } else {
            document.getElementById('search-results').innerHTML = '';
        }
    });

    const contentMap = {
        <?php foreach ($aulas as $index => $aula): ?>
            content<?php echo $index + 1; ?>: `
                <h2><?php echo htmlspecialchars($aula['titulo']); ?></h2>
                <h3><?php echo htmlspecialchars($aula['descricao']); ?></h3>
                <iframe src="<?php echo htmlspecialchars($aula['iframe']); ?>" frameborder="0" allowfullscreen></iframe>
            `,
        <?php endforeach; ?>
    };

    document.querySelectorAll('#sidebarMenu nav ul li').forEach(li => {
        li.addEventListener('click', function () {
            const contentKey = this.getAttribute('data-content');
            if (contentMap[contentKey]) {
                document.querySelector('main').innerHTML = contentMap[contentKey];
                document.getElementById('sidebarMenu').classList.add('hidden');
            }
        });
    });

    document.getElementById('theme-toggle').addEventListener('click', function () {
        document.body.classList.toggle('light-theme');
        if (document.body.classList.contains('light-theme')) {
            this.textContent = '🌞';
            document.body.style.backgroundColor = '#f9f9f9'; // Mudar a cor do background para branco no tema claro
        } else {
            this.textContent = '🌙';
            document.body.style.backgroundColor = ''; // Resetar a cor do background no tema escuro
        }
    });

    document.getElementById('menu-toggle').addEventListener('click', function () {
        const sidebarMenu = document.getElementById('sidebarMenu');
        sidebarMenu.classList.toggle('hidden');
        document.body.classList.toggle('aside-hidden', sidebarMenu.classList.contains('hidden'));
    });

    document.getElementById('close-sidebar').addEventListener('click', function () {
        const sidebarMenu = document.getElementById('sidebarMenu');
        sidebarMenu.classList.remove('hidden');
        document.body.classList.remove('aside-hidden');
    });

    document.querySelectorAll('.help-options button').forEach(button => {
        button.addEventListener('click', function () {
        });
    });

    document.querySelector('.sign-in-button').addEventListener('click', function () {
    });
});

</script>
<script src="https://kit.fontawesome.com/2bb7425346.js" crossorigin="anonymous"></script>

<div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
</body>
</html>
