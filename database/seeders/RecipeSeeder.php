<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\Tag;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Download user
        $test = User::where('email', 'test@gmail.com')->firstOrFail();
        $test2 = User::where('email', 'test2@gmail.com')->firstOrFail();

        // Download categories by slug
        $breakfast = Category::where('slug', 'breakfast')->first();
        $lunch = Category::where('slug', 'lunch')->first();
        $dinner = Category::where('slug', 'dinner')->first();
        $dessert = Category::where('slug', 'dessert')->first();
        $smoothie = Category::where('slug', 'smoothie')->first();
        $vegetarian = Category::where('slug', 'vegetarian')->first();

        // Recipe definitions
        $recipesData = [
            [
                'user' => $test,
                'category' => $breakfast,
                'title' => 'Owsianka z owocami',
                'preparation' => "Płatki owsiane zalej gorącym mlekiem i odstaw na 5 minut.\n"
                    . "Dodaj pokrojone owoce, miód i posyp ziarnami chia.\n"
                    . "Podawaj od razu w miseczce.",
                'time' => 10,
                'tags' => ['healthy', 'quick', 'vegetarian'],
                'ingredients' => [
                    ['name' => 'płatki owsiane', 'quantity' => '80', 'unit' => 'gram'],
                    ['name' => 'mleko', 'quantity' => '200', 'unit' => 'ml'],
                    ['name' => 'banan', 'quantity' => '1', 'unit' => 'sztuka'],
                    ['name' => 'truskawki', 'quantity' => '100', 'unit' => 'gram'],
                    ['name' => 'miód', 'quantity' => '1', 'unit' => 'łyżka'],
                    ['name' => 'ziarna chia', 'quantity' => '1', 'unit' => 'łyżeczka'],
                ],
            ],
            [
                'user' => $test,
                'category' => $lunch,
                'title' => 'Sałatka z kurczakiem i awokado',
                'preparation' => "Pierś z kurczaka ugotuj lub usmaż na grillu, pokrój w paski.\n"
                    . "Awokado obierz i pokrój w kostkę, skrop sokiem z cytryny.\n"
                    . "Wymieszaj rukolę, kurczaka, awokado i pomidorki cherry.\n"
                    . "Polej oliwą i dopraw solą, pieprzem.",
                'time' => 25,
                'tags' => ['healthy', 'high-protein'],
                'ingredients' => [
                    ['name' => 'pierś z kurczaka', 'quantity' => '200', 'unit' => 'gram'],
                    ['name' => 'awokado', 'quantity' => '1', 'unit' => 'sztuka'],
                    ['name' => 'rukola', 'quantity' => '50', 'unit' => 'gram'],
                    ['name' => 'pomidorki cherry', 'quantity' => '100', 'unit' => 'gram'],
                    ['name' => 'oliwa z oliwek', 'quantity' => '2', 'unit' => 'łyżki'],
                    ['name' => 'sok z cytryny', 'quantity' => '1', 'unit' => 'łyżka'],
                ],
            ],
            [
                'user' => $test,
                'category' => $dinner,
                'title' => 'Makaron carbonara',
                'preparation' => "Makaron ugotuj al dente w osolonej wodzie.\n"
                    . "Boczek podsmaż na suchej patelni do chrupkości.\n"
                    . "Żółtka wymieszaj z parmezanem i pieprzem.\n"
                    . "Odlej szklankę wody z makaronu, odcedź makaron.\n"
                    . "Zdejmij patelnię z ognia, dodaj makaron i boczek.\n"
                    . "Wlej mieszankę jajeczną i szybko wymieszaj, rozcieńczając wodą z makaronu.",
                'time' => 30,
                'tags' => ['italian', 'pasta', 'classic'],
                'ingredients' => [
                    ['name' => 'makaron spaghetti', 'quantity' => '200', 'unit' => 'gram'],
                    ['name' => 'boczek wędzony', 'quantity' => '150', 'unit' => 'gram'],
                    ['name' => 'żółtka jaj', 'quantity' => '3', 'unit' => 'sztuki'],
                    ['name' => 'parmezan', 'quantity' => '50', 'unit' => 'gram'],
                    ['name' => 'czarny pieprz', 'quantity' => '1', 'unit' => 'łyżeczka'],
                ],
            ],
            [
                'user' => $test2,
                'category' => $dessert,
                'title' => 'Czekoladowe brownie',
                'preparation' => "Czekoladę i masło rozpuść w kąpieli wodnej, ostudź.\n"
                    . "Jajka ubij z cukrem na puszystą masę.\n"
                    . "Połącz z czekoladą, dodaj mąkę i kakao, wymieszaj.\n"
                    . "Przelej do formy wyłożonej papierem.\n"
                    . "Piecz 20 minut w 180°C — środek ma być lekko wilgotny.",
                'time' => 40,
                'tags' => ['chocolate', 'baking', 'sweet'],
                'ingredients' => [
                    ['name' => 'czekolada gorzka', 'quantity' => '200', 'unit' => 'gram'],
                    ['name' => 'masło', 'quantity' => '150', 'unit' => 'gram'],
                    ['name' => 'jajka', 'quantity' => '3', 'unit' => 'sztuki'],
                    ['name' => 'cukier', 'quantity' => '150', 'unit' => 'gram'],
                    ['name' => 'mąka pszenna', 'quantity' => '80', 'unit' => 'gram'],
                    ['name' => 'kakao', 'quantity' => '2', 'unit' => 'łyżki'],
                ],
            ],
            [
                'user' => $test2,
                'category' => $smoothie,
                'title' => 'Zielone smoothie ze szpinakiem',
                'preparation' => "Wszystkie składniki wrzuć do blendera.\n"
                    . "Blenduj przez 60 sekund na najwyższych obrotach.\n"
                    . "Przelej do szklanki i podawaj od razu.",
                'time' => 5,
                'tags' => ['vegan', 'healthy', 'quick'],
                'ingredients' => [
                    ['name' => 'szpinak świeży', 'quantity' => '60', 'unit' => 'gram'],
                    ['name' => 'banan', 'quantity' => '1', 'unit' => 'sztuka'],
                    ['name' => 'jabłko', 'quantity' => '1', 'unit' => 'sztuka'],
                    ['name' => 'mleko roślinne', 'quantity' => '250', 'unit' => 'ml'],
                    ['name' => 'imbir świeży', 'quantity' => '1', 'unit' => 'cm'],
                ],
            ],
            [
                'user' => $test2,
                'category' => $vegetarian,
                'title' => 'Zupa krem z dyni',
                'preparation' => "Dynię obierz, pokrój w kostkę i upiecz 25 min w 200°C.\n"
                    . "Cebulę i czosnek podsmaż na maśle do zeszklenia.\n"
                    . "Dodaj upieczoną dynię, zalej bulionem, gotuj 10 minut.\n"
                    . "Zblenduj na krem, dopraw solą, pieprzem i gałką muszkatołową.\n"
                    . "Podawaj z łyżką śmietany i pestkami dyni.",
                'time' => 50,
                'tags' => ['soup', 'vegetarian', 'autumn'],
                'ingredients' => [
                    ['name' => 'dynia hokkaido', 'quantity' => '800', 'unit' => 'gram'],
                    ['name' => 'cebula', 'quantity' => '1', 'unit' => 'sztuka'],
                    ['name' => 'czosnek', 'quantity' => '2', 'unit' => 'ząbki'],
                    ['name' => 'bulion warzywny', 'quantity' => '500', 'unit' => 'ml'],
                    ['name' => 'masło', 'quantity' => '2', 'unit' => 'łyżki'],
                    ['name' => 'śmietana 18%', 'quantity' => '4', 'unit' => 'łyżki'],
                    ['name' => 'pestki dyni', 'quantity' => '2', 'unit' => 'łyżki'],
                    ['name' => 'gałka muszkatołowa', 'quantity' => '1', 'unit' => 'szczypta'],
                ],
            ],
        ];

        // Creating recipes
        $createdRecipes = [];

        foreach ($recipesData as $data) {
            // 1. Create or find a recipe
            $recipe = Recipe::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'user_id' => $data['user']->id,
                    'category_id' => $data['category']->id,
                    'title' => $data['title'],
                    'preparation' => $data['preparation'],
                    'time' => $data['time'],
                    'image' => null,
                ]
            );

            // 2. Ingredients - firstOrCreate in dictionary, then pivot
            foreach ($data['ingredients'] as $ing) {
                $ingredient = Ingredient::firstOrCreate(['name' => $ing['name']]);

                // Use updateOrCreate on the pivot to avoid duplicates
                DB::table('recipe_ingredients')->updateOrInsert(
                    ['recipe_id' => $recipe->id, 'ingredient_id' => $ingredient->id],
                    ['quantity' => $ing['quantity'], 'unit' => $ing['unit']]
                );
            }

            // 3. Tags - firstOrCreate in dictionary, then sync via Eloquent
            $tagIds = [];
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $recipe->tags()->sync($tagIds);

            $createdRecipes[] = $recipe;
        }

        // Favorites - Some examples
        $this->seedFavourites($test, $test2, $createdRecipes);
        // Comments with answers
        $this->seedComments($test, $test2, $createdRecipes);
    }

        private function seedFavourites($test, $test2, $recipes): void
        {
            // test likes recipes test2
            DB::table('favourites')->insertOrIgnore([
                ['id' => Str::uuid(), 'user_id' => $test->id, 'recipe_id' => $recipes[2]->id, 'created_at' => now()], // carbonara
                ['id' => Str::uuid(), 'user_id' => $test->id, 'recipe_id' => $recipes[3]->id, 'created_at' => now()], // brownie
            ]);
        }

        private function seedComments($test, $test2, $recipes): void
        {
            // comment test2 to recipe test + answer test
            $comment1Id = Str::uuid()->toString();
            DB::table('comments')->insertOrIgnore([[
                'id' => $comment1Id,
                'recipe_id' => $recipes[0]->id,
                'user_id' => $test2->id,
                'guest_name' => null,
                'content' => 'Pyszna owsianka! Dodałem jeszcze łyżkę masła orzechowego i polecam �',
                'parent_id' => null,
                'created_at' => now()->subDays(3),
            ]]);

            DB::table('comments')->insertOrIgnore([[
                'id' => Str::uuid(),
                'recipe_id' => $recipes[0]->id,
                'user_id' => $test->id,
                'guest_name' => null,
                'content' => 'Świetny pomysł z masłem orzechowym, sama muszę spróbować!',
                'parent_id' => $comment1Id,
                'created_at' => now()->subDays(2),
            ]]);

            // quest comment
            DB::table('comments')->insertOrIgnore([[
                'id' => Str::uuid(),
                'recipe_id' => $recipes[2]->id,
                'user_id' => null,
                'guest_name' => 'Tomek',
                'content' => 'Zrobiłem wczoraj na kolację, rodzina zachwycona. Przepis godny polecenia!',
                'parent_id' => null,
                'created_at' => now()->subDays(1),
            ]]);
        }
}
