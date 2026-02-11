<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Category;
use App\Models\VariationGroup;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $categories = Category::all()->keyBy('name');

        $menus = [
            // COFFEE
            [
                'menu_name' => 'Copsuz',
                'description' => 'Kopi susu dengan gula aren',
                'price' => 29565,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1587462970332-9f0516628dcb?q=80&w=720&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Espresso',
                'description' => 'Kopi pekat shot dengan rasa kuat dan aroma khas',
                'price' => 19130,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1595434091143-b375ced5fe5c?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Espresso On The Rock',
                'description' => 'Espresso disajikan dengan es batu, segar dan bold',
                'price' => 20000,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://plus.unsplash.com/premium_photo-1669687924613-4a66b6278051?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Americano',
                'description' => 'Espresso dengan air panas, rasa ringan namun tetap beraroma kuat',
                'price' => 18261,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1587985782608-20062892559d?q=80&w=1074&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Americano',
                'description' => 'Americano dingin dengan sensasi segar dan ringan',
                'price' => 19130,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1517959105821-eaf2591984ca?q=80&w=1473&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Cappucino',
                'description' => 'Espresso, susu, dan foam lembut dengan rasa seimbang',
                'price' => 28696,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1557006021-b85faa2bc5e2?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Cappucino',
                'description' => 'Versi dingin cappuccino dengan kesejukan creamy',
                'price' => 30435,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://130529051.cdn6.editmysite.com/uploads/1/3/0/5/130529051/V3O3FIVEAP2WHOYTSKFLMVCN.jpeg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Cappucino Oat',
                'description' => 'Cappuccino hangat dengan susu oat, lebih ringan',
                'price' => 31304,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://img.freepik.com/premium-photo/cup-coffee-latte-with-heart-shape-coffee-beans_230573-608.jpg?w=1480',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Cappucino Oat',
                'description' => 'Cappuccino dingin dengan oat milk, lembut segar',
                'price' => 32174,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://img-global.cpcdn.com/steps/3279a948213a64ca/400x400cq80/photo.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Magic',
                'description' => 'Kopi double ristretto dengan susu, lebih bold namun creamy',
                'price' => 28696,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1586558284757-8ccc2f8d680d?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Vietnam Drip',
                'description' => 'Kopi pekat ala Vietnam dengan cita rasa khas',
                'price' => 13913,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQKyojEV7TKOsgnjQP98CfRc7aCB9E7uDPT5A&s',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Coffee Tonic',
                'description' => 'Espresso dengan tonic water, segar, pahit-manis berkarbonasi',
                'price' => 24348,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1629022194335-b2eca031e320?q=80&w=1025&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'V60 Lokal',
                'description' => 'Manual brew V60 dengan biji kopi lokal, rasa autentik Indonesia',
                'price' => 30435,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://upload.jaknot.com/2024/01/images/products/4df1c3/original/one-two-cups-filter-penyaring-kopi-v60-coffee-filter-wooden-bracket-se102.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'V60 Spezial',
                'description' => 'V60 dengan pilihan biji spesial, clean dan aromatik',
                'price' => 40870,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://plus.unsplash.com/premium_photo-1733317435318-531c85f0f00a?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Japanese Lokal',
                'description' => 'Kopi ala Japanese iced brew dengan biji lokal',
                'price' => 32174,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://omakase-forest.com/cdn/shop/articles/Coffee_Blog_Hero_3f9008ff-6875-4695-986d-d7ad9c692780.jpg?v=1719186306&width=1100',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Japanese Spezial',
                'description' => 'Japanese iced brew dengan biji spesial',
                'price' => 43478,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://thumbs.dreamstime.com/b/japanese-aesthetic-coffee-shop-wabi-sabi-design-clay-cup-coffee-large-ceramic-plate-cracked-glaze-pattern-365539116.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Vanilla Latte',
                'description' => 'Latte lembut dengan sentuhan manis vanilla',
                'price' => 24348,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1504194472231-5a5294eddc43?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Hazelnut Latte',
                'description' => 'Latte rasa kacang hazelnut yang gurih',
                'price' => 24348,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://img.freepik.com/premium-photo/delicious-nutty-coffee-topped-with-cream-nuts-wooden-table_1082141-73523.jpg?semt=ais_hybrid&w=740&q=80',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Butterscotch Latte',
                'description' => 'Latte rasa manis karamel butterscotch',
                'price' => 24348,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://syruvia.com/cdn/shop/articles/Butterscotch_Latte.webp?v=1768572843',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Caramel Latte',
                'description' => 'Latte sirup karamel manis dan lembut',
                'price' => 24348,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://mocktail.net/wp-content/uploads/2022/06/Iced-Caramel-Latte_4-ig.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Vanilla Latte',
                'description' => 'Latte dingin manis lembut vanilla',
                'price' => 26087,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://www.haylskitchen.com/wp-content/uploads/2020/02/Iced-Vanilla-Latte-Protein-Shake-1.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Hazelnut Latte',
                'description' => 'Latte segar dengan rasa hazelnut gurih',
                'price' => 26087,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://athome.starbucks.com/sites/default/files/2025-04/SBX_2025_Crema_Collection_Frothed_Coffee_CAH_Recipe_Gradient_Image.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Butterscotch Latte',
                'description' => 'Latte rasa butterscotch manis karamel',
                'price' => 26087,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://www.putrafarmayogyakarta.co.id/wp-content/uploads/2022/08/resep-olahan-butterscotch-latte-3.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Caramel Latte',
                'description' => 'Latte dengan sirup karamel manis dan creamy',
                'price' => 26087,
                'category_id' => $categories['Coffee']->id,
                'image_url' => 'https://cdn.mygingergarlickitchen.com/images/800px/800px-iced-caramel-latte-recipe-5.jpg',
                'is_available' => true,
            ],

            // NON-COFFEE
            [
                'menu_name' => 'Hot Chocolate',
                'description' => 'Minuman cokelat hangat, manis dan creamy',
                'price' => 21739,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Strawberry Smoothies',
                'description' => 'Smoothies segar dengan rasa manis-asam stroberi alami',
                'price' => 32609,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1611928237590-087afc90c6fd?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Blueberry Smoothies',
                'description' => 'Smoothies rasa khas blueberry yang menyegarkan',
                'price' => 36368,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1588929473475-d16ffd5d068c?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Choco Milk',
                'description' => 'Susu cokelat klasik yang lembut dan manis',
                'price' => 24348,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://plus.unsplash.com/premium_photo-1695750678195-beb8ba487094?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Tea',
                'description' => 'Teh hangat klasik dengan rasa menenangkan',
                'price' => 21739,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1597318181412-49af291f617f?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Tea',
                'description' => 'Teh dingin segar, cocok untuk pelepas dahaga',
                'price' => 21739,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://plus.unsplash.com/premium_photo-1726866175183-7fe0f6f9746f?q=80&w=766&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Lemon Tea',
                'description' => 'Teh hangat dengan perasan lemon, segar menyehatkan',
                'price' => 21739,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://images.unsplash.com/photo-1615484477112-677decb29c57?q=80&w=880&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Lemon Tea',
                'description' => 'Teh dingin lemon segar, rasa asam manis',
                'price' => 21739,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://plus.unsplash.com/premium_photo-1664392087859-815b337c3324?q=80&w=780&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Lychee Tea',
                'description' => 'Teh hangat berpadu rasa manis buah leci',
                'price' => 21739,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://i0.wp.com/ricelifefoodie.com/wp-content/uploads/2024/10/tall-glass-of-lychee-iced-tea.jpg?resize=900%2C1024&ssl=1',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Lychee Tea',
                'description' => 'Teh dingin dengan sentuhan rasa leci segar',
                'price' => 21739,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://ganeshaeksanskriti.com/cdn/shop/files/GANESHA-24NOV20229LYCHEEICETEA14855.jpg?v=1703910439&width=1946',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Hot Matcha',
                'description' => 'Matcha hangat dengan rasa khas teh hijau Jepang',
                'price' => 32609,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://www.romylondonuk.com/wp-content/uploads/2025/04/Matcha-Latte-Starbucks-Recipe_THUMB.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Ice Matcha',
                'description' => 'Matcha dingin yang creamy dan menyegarkan',
                'price' => 34783,
                'category_id' => $categories['Non-Coffee']->id,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ84C6evsejAHm54FCz78sQDWtsdAOccy56-A&s',
                'is_available' => true,
            ],

            // SIGNATURE
            [
                'menu_name' => 'King Of Kopi Duga / Masa',
                'description' => 'Kopi susu dengan rasa kopi yang tebal, gula atau tanpa gula',
                'price' => 43478,
                'category_id' => $categories['Signature']->id,
                'image_url' => 'https://corkframes.com/cdn/shop/articles/Corkframes_Coffee_Guide_520x500_422ebe38-4cfa-42b5-a266-b9bfecabaf30.jpg?v=1734598727',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Tropikaramel',
                'description' => 'Kopi espresso berpadu dengan manis karamel dan lemon',
                'price' => 24348,
                'category_id' => $categories['Signature']->id,
                'image_url' => 'https://www.mldspot.com/storage/generated/June2021/Panduan%20Singkat%20Cara%20Membuat%20Kopi%20Espresso.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Candytone',
                'description' => 'Minuman manis unik dengan sentuhan permen yang playful',
                'price' => 24348,
                'category_id' => $categories['Signature']->id,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSga301_vH-Shq6l4mzTP7fdQuLNp-z4eVDXw&s',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Gandola Rush',
                'description' => 'Minuman spesial penuh energi dengan rasa khas yang kuat',
                'price' => 24348,
                'category_id' => $categories['Signature']->id,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTMZwDoEscnVyCKs_yy1hmVUwB8eWALjV4Giw&s',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Java Chip',
                'description' => 'Minuman cokelat dengan sensasi crunchy chip',
                'price' => 24348,
                'category_id' => $categories['Signature']->id,
                'image_url' => 'https://www.zulaykitchen.com/cdn/shop/articles/Creamy_Iced_Chocolate_Drink_6828ff01-d9df-4f37-8796-4ee21b7616d6.jpg?v=1743179146&width=2048',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Oza Mango Mint',
                'description' => 'Paduan teh, apel dan kayu manis yang hangat dan aromatik',
                'price' => 30435,
                'category_id' => $categories['Signature']->id,
                'image_url' => 'https://www.pipercooks.com/wp-content/uploads/2024/07/mojito-sq.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Oza Scarlet',
                'description' => 'Racikan teh istimewa dengan aroma lembut dan menenangkan',
                'price' => 30435,
                'category_id' => $categories['Signature']->id,
                'image_url' => 'https://klasika.kompas.id/wp-content/uploads/2020/08/1108-jenis-teh-1.jpg',
                'is_available' => true,
            ],

            // RICE
            [
                'menu_name' => 'Nasi Ayam Goreng Mentega',
                'description' => 'Nasi hangat dengan ayam goreng, dengan saus mentega gurih',
                'price' => 43478,
                'category_id' => $categories['Rice']->id,
                'image_url' => 'https://www.australianeggs.org.au/assets/Uploads/Egg-fried-rice-2.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Nasi Beef Slice Cabe Ijo',
                'description' => 'Nasi dengan irisan daging sapi empuk, tumis cabe hijau pedas',
                'price' => 48696,
                'category_id' => $categories['Rice']->id,
                'image_url' => 'https://www.nutmegnanny.com/wp-content/uploads/2023/06/beef-bulgogi-bowls-8.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Nasi Cumi Kemangi',
                'description' => 'Tumis cumi bumbu pedas dengan wangi daun kemangi',
                'price' => 43478,
                'category_id' => $categories['Rice']->id,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRPTn8G3Aw251F97Vj8KPY0IPtfCdv6O4CDCw&s',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Nasi Daging Jando Pedas',
                'description' => 'Nasi hangat dengan tumis jando sapi pedas',
                'price' => 46087,
                'category_id' => $categories['Rice']->id,
                'image_url' => 'https://img-global.cpcdn.com/recipes/e5e90f0c61e82b81/1200x630cq80/photo.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Nasi Ayam Teriyaki',
                'description' => 'Nasi dengan potongan ayam teriyaki manis gurih',
                'price' => 43478,
                'category_id' => $categories['Rice']->id,
                'image_url' => 'https://asset.kompas.com/crops/-b8jj61ACSloKe1JGBgAGrgXDdU=/3x0:700x465/1200x800/data/photo/2020/12/17/5fdacb3363f6a.jpg',
                'is_available' => true,
            ],

            // PASTA
            [
                'menu_name' => 'Baked Mac N Cheese',
                'description' => 'Makaroni panggang dengan keju mozzarella yang lumer',
                'price' => 31304,
                'category_id' => $categories['Pasta']->id,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRq9nGxYRHt9d7hpKGEc4K_TW9RaQv8VoUqMA&s',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Baked Lasagna',
                'description' => 'Lasagna panggang dengan saus bolognese dan keju berlapis',
                'price' => 31304,
                'category_id' => $categories['Pasta']->id,
                'image_url' => 'https://www.tasteofhome.com/wp-content/uploads/2025/07/Best-Lasagna_EXPS_ATBBZ25_36333_DR_07_01_2b.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Fettuccine Carbonara',
                'description' => 'Fettuccine creamy dengan saus keju, daging asap, dan jamur',
                'price' => 36522,
                'category_id' => $categories['Pasta']->id,
                'image_url' => 'https://kjsfoodjournal.com/wp-content/uploads/2020/09/carbonara.png',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Spaghetti Aglio Olio',
                'description' => 'Pasta dengan bawang putih, cabai, dan minyak zaitun',
                'price' => 36522,
                'category_id' => $categories['Pasta']->id,
                'image_url' => 'https://asset.kompas.com/crops/PlpskSy749F6-f_WpXeIWfLv-gU=/0x0:1000x667/1200x800/data/photo/2021/10/12/61651c9926d93.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Spaghetti Bolognese',
                'description' => 'Pasta klasik dengan saus tomat dan daging cincang',
                'price' => 36522,
                'category_id' => $categories['Pasta']->id,
                'image_url' => 'https://asset.kompas.com/crops/eWh25QLGaUd83ZfRO6yvdxwygKg=/0x22:968x667/1200x800/data/photo/2023/06/02/64793cbdad978.jpg',
                'is_available' => true,
            ],

            // FRIED RICE
            [
                'menu_name' => 'Nasi Goreng DugaMasa',
                'description' => 'Nasi goreng kampung dengan bumbu otentik dengan telur',
                'price' => 27826,
                'category_id' => $categories['Fried Rice']->id,
                'image_url' => 'https://asset.kompas.com/crops/GgoPUrhHFV5EtkSU71XOR8MrNTY=/0x0:1062x708/1200x800/data/photo/2025/06/15/684e9654425ca.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Nasi Goreng Special',
                'description' => 'Nasi goreng spesial dengan sosis, ayam, dan bakso',
                'price' => 25217,
                'category_id' => $categories['Fried Rice']->id,
                'image_url' => 'https://www.seriouseats.com/thmb/x7GelfL9GltWlPXnD9fwOSTKHJU=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/nasi-goreng-recipe-hero-03-b871cfba57fa4272bb2cf4e900879a79.JPG',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Nasi Goreng Kambing',
                'description' => 'Nasi goreng dengan irisan daging kambing empuk, kaya rempah',
                'price' => 33043,
                'category_id' => $categories['Fried Rice']->id,
                'image_url' => 'https://i.guim.co.uk/img/media/535c66550edac692c9c3b6fe84184085eb2ee2ab/0_1267_8815_5290/master/8815.jpg?width=1200&quality=85&auto=format&fit=max&s=918882b1fe2c9db31b3d3188e468f9c0',
                'is_available' => true,
            ],

            // PIZZA
            [
                'menu_name' => 'Beef Margherita Pizza',
                'description' => 'Pizza klasik dengan keju mozzarella, tomat, dan daun basil segar',
                'price' => 59130,
                'category_id' => $categories['Pizza']->id,
                'image_url' => 'https://cherrybombe.com/cdn/shop/articles/unnamed_246e6ba1-4b5e-4cd2-a413-9cac8dee49e6.jpg?v=1719665803',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Beef Pepperoni Pizza',
                'description' => 'Pizza dengan topping beef pepperoni, keju, dan saus tomat',
                'price' => 59130,
                'category_id' => $categories['Pizza']->id,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSAkU71Mo2fxv6Dn0ax_NK7wRnuwy9nK0qh7g&s',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Beef Cheese Pizza',
                'description' => 'Pizza dengan saus keju dan mozzarella lezat',
                'price' => 59130,
                'category_id' => $categories['Pizza']->id,
                'image_url' => 'https://emeals-menubuilder.s3.amazonaws.com/v1/recipes/886947/pictures/large_mac-cheese-pepperoni-beef-pizzas-slice.jpeg',
                'is_available' => true,
            ],

            // WESTERN
            [
                'menu_name' => 'Chicken Parmigiana',
                'description' => 'Daging ayam dilapisi tepung roti disajikan dengan saus tomat dan keju',
                'price' => 33913,
                'category_id' => $categories['Western']->id,
                'image_url' => 'https://cdn-msft.recipecdn.com/recipeimages/sam_FKpU1/7b3f8441-708c-44df-aa45-5ae2ef6bdd76/GridSquare-Medium',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Chicken Cheese',
                'description' => 'Daging ayam dengan saus keju mozzarella',
                'price' => 33913,
                'category_id' => $categories['Western']->id,
                'image_url' => 'https://spicysouthernkitchen.com/wp-content/uploads/cheesy-chicken-19.jpg',
                'is_available' => true,
            ],

            // LIGHT MEALS
            [
                'menu_name' => 'Cireng',
                'description' => 'Gurih renyah dengan isian lembut',
                'price' => 27826,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTyI2KOGiAtQCwhA5wYadkc8SUSOdJXMvPBlQ&s',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Kentang Sosis',
                'description' => 'Perpaduan kentang goreng renyah dan sosis',
                'price' => 33043,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://cdn.pixabay.com/photo/2015/09/16/09/32/food-942465_1280.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Dimsum Siomay',
                'description' => 'Siomay ayam yang padat dan lembut, disajikan hangat',
                'price' => 26087,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSB9xemxFOiZDkKuYl4eug8aMtHZVVREafJgQ&s',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Dimsum Kulit Tahu',
                'description' => 'Dimsum berisi daging cincang dibungkus kulit tahu',
                'price' => 26087,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://img.bdhigh.com/img/1200/vn21gk0vqqh1ww1t/CM36bMo08AnBjLdLCM4FehtSbMLr6pSQhehEbiWgm91jA.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Dimsum Kuo Tie',
                'description' => 'Pangsit goreng berisi daging ayam dan sayuran gurih',
                'price' => 26087,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://beritajatim.com/wp-content/uploads/2023/01/293083990_587690726298230_4999017397972210103_n.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Dimsum Ekado',
                'description' => 'Ayam udang yang digoreng, renyah di luar, gurih di dalam',
                'price' => 26087,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://nilam.sukronjazuli.com/assets/images/product/1727963601464.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Pisang Keju',
                'description' => 'Pisang goreng crispy ditaburi parutan keju melimpah',
                'price' => 27826,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://d1vbn70lmn1nqe.cloudfront.net/prod/wp-content/uploads/2023/10/30053823/3-Resep-Pisang-Keju-Crispy-yang-Lezat-untuk-Camilan-Keluarga-.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Risoles Ragout Mayo',
                'description' => 'Risoles dengan ragout ayam lembut dan saus mayo',
                'price' => 18261,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://asset.kompas.com/crops/VfRpjDpnHs8LdpNhqTX_E9sorpc=/0x254:800x787/1200x800/data/photo/2020/09/16/5f61d6aa675e8.jpg',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Risoles Ragout Ayam Pedas',
                'description' => 'Risoles ragout ayam dengan cita rasa pedas',
                'price' => 18261,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://img.inews.co.id/media/600/files/networks/2023/06/16/c0d29_risoles.png',
                'is_available' => true,
            ],
            [
                'menu_name' => 'Chicken Wings Cheese Sauce',
                'description' => 'Sayap ayam renyah dilapisi saus keju parmesan gurih',
                'price' => 39130,
                'category_id' => $categories['Light Meals']->id,
                'image_url' => 'https://assets.unileversolutions.com/recipes-v2/232553.jpg',
                'is_available' => true,
            ],
        ];

        // Get variation groups
        $sugar = VariationGroup::where('name', 'Sugar Level')->first();
        $ice = VariationGroup::where('name', 'Ice Level')->first();
        $size = VariationGroup::where('name', 'Size')->first();
        $spice = VariationGroup::where('name', 'Spice Level')->first();

        foreach ($menus as $menuData) {
            $menu = Menu::create($menuData);
            $categoryName = $categories->where('id', $menu->category_id)->first()->name;

            // Add variations based on category
            if (in_array($categoryName, ['Coffee', 'Non-Coffee', 'Signature'])) {
                // All drinks get Sugar (if required) and Size
                if ($sugar)
                    $menu->variationGroups()->attach($sugar->id);
                if ($size)
                    $menu->variationGroups()->attach($size->id);

                // Only Ice drinks get Ice Level
                if (str_contains(strtolower($menu->menu_name), 'ice') || str_contains(strtolower($menu->description), 'dingin')) {
                    if ($ice)
                        $menu->variationGroups()->attach($ice->id);
                }
            }

            // Food items might have Spice Level
            if (in_array($categoryName, ['Rice', 'Fried Rice', 'Pasta', 'Western', 'Light Meals'])) {
                if (
                    str_contains(strtolower($menu->menu_name), 'pedas') || str_contains(strtolower($menu->description), 'pedas') ||
                    str_contains(strtolower($menu->menu_name), 'cabe') || str_contains(strtolower($menu->menu_name), 'spicy')
                ) {
                    if ($spice)
                        $menu->variationGroups()->attach($spice->id);
                }
            }
        }
    }
}