<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('books')->insert([
            [
                'name' => 'La mansión de las pesadillas',
                'description' => 'Unos agentes de investigación reciben la orden de ir a investigar el caso de desaparición de una familia en una mansión abandonada a lo lejos de la ciudad. En el proceso de exploración, los agentes se verán involucrados en diferentes situaciones paranormales.
Los protagonistas se darán cuenta de que no están solos en la mansión, en ese momento las cosas se empezarán a complicar.
¿Lograrán los agentes resolver el caso?',
                'isbn' => '‎ 979-8704785880',
                'author' => 'Madirex',
                'publisher' => 'Madirex',
                'category_name' => 'Terror',
                'image' => 'images/1.png',
                'price' => 10.4,
                'stock' => 50,
                'active' => true,
                'created_at' => '2020-10-17 10:00:00',
                'updated_at' => '2020-10-17 10:00:00'
            ],
            [
                'name' => 'Abre la mente, piensa diferente',
                'description' => 'Abre la mente, piensa diferente aborda temas que muy poca gente suele pararse a reflexionar. Temas tan delicados como las religiones, la política, las relaciones sociales o incluso la propia muerte. Muchas personas piensan que creen saber cómo funciona la vida, pero ¿esto realmente es así?
Vivimos en un mundo extraordinario con cambios exponenciales e inciertos. El desarrollo tecnológico es cada vez mayor y no sabemos qué nos puede llegar a deparar el futuro.
¿Estás preparado para los cambios que vienen?',
                'isbn' => '979-8523992919',
                'author' => 'Madirex',
                'publisher' => 'Madirex',
                'category_name' => 'Desarrollo personal',
                'image' => 'images/2.png',
                'price' => 10.4,
                'stock' => 10,
                'active' => true,
                'created_at' => '2021-06-25 10:00:00',
                'updated_at' => '2021-06-25 10:00:00'
            ],
            [
                'name' => '¿El asesino sigue aquí?',
                'description' => 'Manuel es un detective que vive junto a su hijo Toni en el pueblo Risirú. En el pasado, ambos sufrieron la pérdida de un ser querido.
La mujer de Manuel había sido asesinada.
Pasado un tiempo y con ayuda de profesionales, consiguieron superar el trauma que les había dejado ese asesino.Risirú tenía un pasado muy oscuro, lleno de delincuencia.
Manuel consiguió erradicar por completo la mala fama que tenía ese pueblo.Años después... Volvió a morir alguien.Manuel y Toni se preguntaron:¿El asesino sigue aquí?',
                'isbn' => '979-8354235865',
                'author' => 'Madirex',
                'publisher' => 'Madirex',
                'category_name' => 'Terror',
                'image' => 'images/3.png',
                'price' => 15.18,
                'stock' => 10,
                'active' => true,
                'created_at' => '2022-09-24 10:00:00',
                'updated_at' => '2022-09-24 10:00:00'
            ],
            [
                'name' => 'Cóctel de la Fortuna',
                'description' => 'Este libro no te promete riquezas instantáneas ni cambios mágicos en tu vida económica. Desde mi perspectiva como autor, comparto la filosofía que considero clave para construir una fortuna.
Prepárate para un viaje que no transformará tu situación financiera de la noche a la mañana, sino que te guiará por el camino del esfuerzo y la constancia hacia una salud financiera óptima.
Te desafiaré a cuestionar paradigmas mentales sobre el manejo del dinero en la sociedad.
Explorarás el pasado y el futuro del dinero y las nuevas oportunidades que se vienen gracias a la inteligencia artificial.
Prepárate para un viaje transformador hacia la prosperidad financiera.',
                'isbn' => '979-8868455551',
                'author' => 'Madirex',
                'publisher' => 'Madirex',
                'category_name' => 'Desarrollo personal',
                'image' => 'images/4.png',
                'price' => 12,
                'stock' => 10,
                'active' => true,
                'created_at' => '2023-11-23 10:00:00',
                'updated_at' => '2023-11-23 10:00:00'
            ],
        ]);
    }
}
