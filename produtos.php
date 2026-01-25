<?php
require __DIR__ . '/config.php';
require_login();
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'Usuário');
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Melhores Ofertas</title>
    <!-- FavIcon -->
    <link
      rel="icon"
      href="assets/icons/favicon/logo-Shot-Keys.ico"
      type="image/x-icon"
    />

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />

    <!-- CSS Customizado (para cores e ajustes finos) -->
    <link rel="stylesheet" href="assets/css/style.css" />
  </head>
  <body class="bg-custom-primary">
    <header
      class="navbar navbar-expand-lg navbar-dark bg-custom-primary fixed-top shadow-sm py-3"
    >
      <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="index.html" aria-label="Página Inicial">
          <i class="fas fa-house fa-lg text-white"></i>
        </a>

        <!-- Botão Mobile -->
        <button
          class="navbar-toggler shadow-none border-0"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu e Ícones Laterais -->
        <div class="collapse navbar-collapse" id="navbarNav">
          <!-- Links principais -->
          <ul class="navbar-nav mx-auto gap-lg-4 gap-2 text-center">
            <li class="nav-item">
              <a class="nav-link active fw-semibold" href="#hero">Início</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold" href="#nuuvem">Nuuvem</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold" href="#thunderkeys"
                >Thunderkeys</a
              >
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold" href="#gmg">Green Man Gaming</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold" href="#faq">FAQ</a>
            </li>
          </ul>

          <!-- Ícones laterais (Carrinho, Usuário) -->
          <ul class="navbar-nav gap-3 align-items-lg-center">
            <li class="nav-item">
              <a class="nav-link" href="#" aria-label="Carrinho">
                <i class="fas fa-shopping-cart fa-lg"></i>
              </a>
            </li>
            <li class="nav-item">
              <div class="d-flex align-items-center gap-2 text-white">
                <i class="fas fa-user-circle fa-lg"></i>
                <span class="fw-semibold"><?= $user_name ?></span>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-custom-accent" href="dashboard.php">Painel</a>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <main class="pt-5">
      <!-- Adicionado pt-5 para o conteúdo não ficar escondido atrás da navbar fixed-top -->
      <section
        id="hero"
        class="hero-section text-center d-flex flex-column justify-content-center align-items-center py-5 min-vh-75"
      >
        <div class="container">
          <h2 class="display-3 fw-bold text-white mb-4">
            Encontre as melhores ofertas
          </h2>
          <p class="lead text-white mb-5">
            Descubra as ofertas mais quentes das suas lojas favoritas e
            economize em grandes títulos.
          </p>
          <a href="#nuuvem" class="btn btn-custom-accent btn-lg shadow-lg"
            >Ver Ofertas Agora</a
          >
        </div>
      </section>

      <section
        id="nuuvem"
        class="store-section py-5 border-bottom border-custom-secondary"
      >
        <div class="container">
          <h3
            class="text-center display-5 fw-bold text-custom-accent mb-5 text-uppercase"
          >
            Ofertas da Nuuvem
          </h3>
          <div
            class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4"
          >
            <!-- Produto 1 Nuuvem -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img
                  src=""
                  class="card-img-top"
                  alt="Capa do Jogo Cyberpunk 2077"
                />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">Cyberpunk 2077</h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Explore a vasta e futurística Night City neste épico RPG de
                    mundo aberto.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 199,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 99,99</span
                    >
                  </div>
                  <a
                    href=""
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na Nuuvem</a
                  >
                </div>
              </div>
            </div>
            <!-- Produto 2 Nuuvem -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img
                  src="#"
                  class="card-img-top"
                  alt="Capa do Jogo Elden Ring"
                />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">Elden Ring</h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Um vasto mundo de fantasia aguarda, com perigos e maravilhas
                    em cada esquina.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 249,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 149,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na Nuuvem</a
                  >
                </div>
              </div>
            </div>
            <!-- Produto 3 Nuuvem -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img
                  src=""
                  class="card-img-top"
                  alt="Capa do Jogo Hogwarts Legacy"
                />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">Hogwarts Legacy</h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Viva a magia e descubra segredos em um mundo bruxo como
                    nunca antes.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 299,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 189,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na Nuuvem</a
                  >
                </div>
              </div>
            </div>
            <!-- Produto 4 Nuuvem -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img
                  src="#"
                  class="card-img-top"
                  alt="Capa do Jogo God of War Ragnarok"
                />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">
                    God of War Ragnarok
                  </h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Kratos e Atreus embarcam em uma jornada épica para evitar o
                    fim dos mundos.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 249,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 124,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na Nuuvem</a
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section
        id="thunderkeys"
        class="store-section py-5 border-bottom border-custom-secondary"
      >
        <div class="container">
          <h3
            class="text-center display-5 fw-bold text-custom-accent mb-5 text-uppercase"
          >
            Ofertas da Thunderkeys
          </h3>
          <div
            class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4"
          >
            <!-- Produto 1 Thunderkeys -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img
                  src=""
                  class="card-img-top"
                  alt="Capa do Jogo Red Dead Redemption 2"
                />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">
                    Red Dead Redemption 2
                  </h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Experimente a vida de fora da lei no coração implacável da
                    América.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 299,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 119,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na Thunderkeys</a
                  >
                </div>
              </div>
            </div>
            <!-- Produto 2 Thunderkeys -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img src="" class="card-img-top" alt="Capa do Jogo Starfield" />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">Starfield</h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Embarque em uma jornada épica pelas estrelas neste RPG da
                    Bethesda.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 349,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 249,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na Thunderkeys</a
                  >
                </div>
              </div>
            </div>
            <!-- Produto 3 Thunderkeys -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img
                  src=""
                  class="card-img-top"
                  alt="Capa do Jogo Baldur's Gate 3"
                />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">Baldur's Gate 3</h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Aventuras épicas e escolhas morais aguardam neste aclamado
                    RPG.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 229,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 179,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na Thunderkeys</a
                  >
                </div>
              </div>
            </div>
            <!-- Produto 4 Thunderkeys -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img
                  src=""
                  class="card-img-top"
                  alt="Capa do Jogo Forza Horizon 5"
                />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">Forza Horizon 5</h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Descubra as paisagens vibrantes e em constante evolução do
                    México.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 249,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 124,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na Thunderkeys</a
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section
        id="gmg"
        class="store-section py-5 border-bottom border-custom-secondary"
      >
        <div class="container">
          <h3
            class="text-center display-5 fw-bold text-custom-accent mb-5 text-uppercase"
          >
            Ofertas da Green Man Gaming
          </h3>
          <div
            class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4"
          >
            <!-- Produto 1 GMG -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img
                  src=""
                  class="card-img-top"
                  alt="Capa do Jogo Assassin's Creed Mirage"
                />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">
                    Assassin's Creed Mirage
                  </h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Explore as ruas de Bagdá e torne-se um Mestre Assassino.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 229,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 159,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na GMG</a
                  >
                </div>
              </div>
            </div>
            <!-- Produto 2 GMG -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img src="" class-img-top alt="Capa do Jogo Diablo IV" />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">Diablo IV</h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Retorne a Santuário e lute contra as forças do inferno neste
                    RPG de ação sombrio.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 349,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 219,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na GMG</a
                  >
                </div>
              </div>
            </div>
            <!-- Produto 3 GMG -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img
                  src=""
                  class-img-top
                  alt="Capa do Jogo Resident Evil 4 Remake"
                />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">
                    Resident Evil 4 Remake
                  </h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    Experimente o terror de sobrevivência clássico reimaginado
                    para uma nova era.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 229,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 149,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na GMG</a
                  >
                </div>
              </div>
            </div>
            <!-- Produto 4 GMG -->
            <div class="col">
              <div
                class="card h-100 bg-custom-secondary border-0 shadow-lg product-card"
              >
                <img src="" class-img-top alt="Capa do Jogo Street Fighter 6" />
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title text-white fs-4">Street Fighter 6</h4>
                  <p class="card-text text-light-emphasis flex-grow-1">
                    A próxima evolução dos jogos de luta chegou, com novos modos
                    e lutadores.
                  </p>
                  <div
                    class="d-flex justify-content-center align-items-baseline mb-3"
                  >
                    <span
                      class="text-decoration-line-through text-muted me-2 fs-6"
                      >R\$ 269,99</span
                    >
                    <span class="text-custom-accent fw-bold fs-4"
                      >R\$ 169,99</span
                    >
                  </div>
                  <a
                    href="#"
                    class="btn btn-custom-accent mt-auto"
                    target="_blank"
                    >Comprar na GMG</a
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="faq" class="faq-section py-5">
        <div class="container">
          <h3
            class="text-center display-5 fw-bold text-custom-accent mb-5 text-uppercase"
          >
            Perguntas Frequentes
          </h3>
          <div class="accordion" id="faqAccordion">
            <div
              class="accordion-item bg-custom-secondary mb-3 border-0 shadow-sm faq-item"
            >
              <h4 class="accordion-header" id="headingOne">
                <button
                  class="accordion-button bg-custom-secondary text-white fw-bold fs-5"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseOne"
                  aria-expanded="true"
                  aria-controls="collapseOne"
                >
                  Como vocês encontram os melhores preços?
                </button>
              </h4>
              <div
                id="collapseOne"
                class="accordion-collapse collapse show"
                aria-labelledby="headingOne"
                data-bs-parent="#faqAccordion"
              >
                <div class="accordion-body text-white-50">
                  Nosso sistema rastreia constantemente as ofertas das
                  principais lojas de jogos, comparando preços e destacando os
                  maiores descontos para você.
                </div>
              </div>
            </div>
            <div
              class="accordion-item bg-custom-secondary mb-3 border-0 shadow-sm faq-item"
            >
              <h4 class="accordion-header" id="headingTwo">
                <button
                  class="accordion-button collapsed bg-custom-secondary text-white fw-bold fs-5"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseTwo"
                  aria-expanded="false"
                  aria-controls="collapseTwo"
                >
                  Os códigos de jogo são oficiais?
                </button>
              </h4>
              <div
                id="collapseTwo"
                class="accordion-collapse collapse"
                aria-labelledby="headingTwo"
                data-bs-parent="#faqAccordion"
              >
                <div class="accordion-body text-white-50">
                  Sim, trabalhamos apenas com lojas parceiras oficiais e
                  licenciadas, garantindo que todos os códigos de jogo sejam
                  legítimos e seguros.
                </div>
              </div>
            </div>
            <div
              class="accordion-item bg-custom-secondary mb-3 border-0 shadow-sm faq-item"
            >
              <h4 class="accordion-header" id="headingThree">
                <button
                  class="accordion-button collapsed bg-custom-secondary text-white fw-bold fs-5"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseThree"
                  aria-expanded="false"
                  aria-controls="collapseThree"
                >
                  Posso comprar diretamente por aqui?
                </button>
              </h4>
              <div
                id="collapseThree"
                class="accordion-collapse collapse"
                aria-labelledby="headingThree"
                data-bs-parent="#faqAccordion"
              >
                <div class="accordion-body text-white-50">
                  Nós te direcionamos para a loja oficial onde a oferta está
                  disponível. A compra é finalizada diretamente no site da loja,
                  garantindo a segurança da sua transação.
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer
      class="bg-custom-secondary py-4 text-center text-muted border-top border-custom-secondary"
    >
      <div class="container">
        <p class="mb-0 text-white">
          &copy; 2024 Game Deals Central. Todos os direitos reservados. Não
          somos afiliados às lojas listadas, apenas agregamos as melhores
          ofertas.
        </p>
      </div>
    </footer>

    <!-- Bootstrap JS -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
