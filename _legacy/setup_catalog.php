<?php
require 'config.php';

echo "<h1>Atualizando Catálogo ShotKeys...</h1>";

try {
    // 1. Adicionar coluna 'category' se não existir
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN category VARCHAR(50) DEFAULT 'Geral'");
        echo "<p>Coluna 'category' adicionada com sucesso.</p>";
    } catch (Exception $e) {
        echo "<p>Coluna 'category' já existe.</p>";
    }

    // 2. Limpar produtos antigos (Opcional, mas bom para limpar teste)
    $pdo->exec("DELETE FROM products WHERE id > 0");
    echo "<p>Produtos antigos removidos.</p>";

    // 3. Inserir Produtos Reais
    $products = [
        // AAA / Lançamentos
        [
            'title' => 'Elden Ring',
            'slug' => 'elden-ring',
            'description' => 'O Jogo do Ano. Explore as Terras Intermédias neste RPG de ação definitivo da FromSoftware.',
            'price' => 22990, // R$ 229,90
            'type' => 'KEY',
            'category' => 'AAA',
            'image' => 'assets/keys/glock/glock.png' // Placeholder
        ],
        [
            'title' => 'Call of Duty: MW3',
            'slug' => 'cod-mw3',
            'description' => 'A guerra mudou. O FPS mais vendido do mundo retorna com combate moderno e frenético.',
            'price' => 29900, // R$ 299,00
            'type' => 'KEY',
            'category' => 'AAA',
            'image' => 'assets/keys/ak47/keyak47.png'
        ],
        [
            'title' => 'Cyberpunk 2077: Phantom Liberty',
            'slug' => 'cyberpunk-2077',
            'description' => 'Torne-se uma lenda nas ruas de Night City. RPG de mundo aberto futurista.',
            'price' => 15990,
            'type' => 'KEY',
            'category' => 'AAA',
            'image' => 'assets/keys/awp/awp.png'
        ],
        
        // Indies
        [
            'title' => 'Hollow Knight',
            'slug' => 'hollow-knight',
            'description' => 'Forje seu caminho em Hallownest. Um metroidvania sombrio, belo e desafiador.',
            'price' => 4699,
            'type' => 'KEY',
            'category' => 'Indie',
            'image' => ''
        ],
        [
            'title' => 'Stardew Valley',
            'slug' => 'stardew-valley',
            'description' => 'Você herdou a antiga fazenda do seu avô. Construa a vida dos seus sonhos.',
            'price' => 2499,
            'type' => 'KEY',
            'category' => 'Indie',
            'image' => ''
        ],
        [
            'title' => 'Celeste',
            'slug' => 'celeste',
            'description' => 'Ajude Madeline a enfrentar seus demônios internos em sua jornada até o topo da Montanha Celeste.',
            'price' => 3699,
            'type' => 'KEY',
            'category' => 'Indie',
            'image' => ''
        ],

        // FPS / Competitivo
        [
            'title' => 'Valorant Points (2050 VP)',
            'slug' => 'valorant-2050',
            'description' => 'Recarregue sua conta e compre skins exclusivas no FPS tático da Riot.',
            'price' => 7490,
            'type' => 'KEY',
            'category' => 'FPS',
            'image' => 'assets/keys/ak47/keyak47.png'
        ],
        [
            'title' => 'CS2 Prime Status',
            'slug' => 'cs2-prime',
            'description' => 'Status Prime para Counter-Strike 2. Drops semanais e matchmaking ranqueado.',
            'price' => 8499,
            'type' => 'KEY',
            'category' => 'FPS',
            'image' => 'assets/keys/glock/glock.png'
        ],

        // Random
        [
            'title' => 'Random Key Premium',
            'slug' => 'random-premium',
            'description' => 'Sorte ou Azar? Receba um jogo aleatório da Steam avaliado em mais de R$ 50.',
            'price' => 999,
            'type' => 'RANDOM_BOX',
            'category' => 'Random',
            'image' => ''
        ],
        [
            'title' => 'Random Key Gold',
            'slug' => 'random-gold',
            'description' => 'Aumente suas chances. Jogo garantido com notas positivas na Steam.',
            'price' => 1999,
            'type' => 'RANDOM_BOX',
            'category' => 'Random',
            'image' => ''
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO products (slug, title, description, price_cents, type, category, image_url, status) VALUES (:slug, :title, :desc, :price, :type, :cat, :img, 'ACTIVE')");

    foreach ($products as $p) {
        $stmt->execute([
            ':slug' => $p['slug'],
            ':title' => $p['title'],
            ':desc' => $p['description'],
            ':price' =>  $p['price'],
            ':type' => $p['type'],
            ':cat' => $p['category'],
            ':img' => $p['image']
        ]);
        echo "Inspirado: " . $p['title'] . "<br>";
    }

    echo "<h3>Catálogo Atualizado com Sucesso!</h3>";
    echo "<a href='products.php'>Ver Loja</a>";

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
