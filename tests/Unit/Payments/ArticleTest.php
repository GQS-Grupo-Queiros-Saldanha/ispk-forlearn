<?php


namespace Tests\Unit;

use App\Modules\Payments\Models\Article;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected $article;

    protected function setUp(): void
    {
        parent::setUp();

        $user = factory(User::class)->create();
        $this->article = factory(Article::class)->create([
            'created_by' => $user->id
        ]);
    }

    /** @test */
    public function a_user_can_create_a_new_article()
    {
        $this->assertCount(1, Article::all());
    }

    /** @test */
    public function an_article_can_have_a_monthly_charge_by_course()
    {
        $this->article->monthly_charge()->create([
            'course_id' => 1,
            'course_year' => 4,
            'day' => 3,
            'start_at' => Carbon::now()->setMonth(3)->startOfMonth(),
            'end_at' => Carbon::now()->setMonth(11)->endOfMonth()
        ]);

        $this->assertCount(1, $this->article->monthly_charge()->get());
    }

    /** @test */
    public function an_article_can_have_extra_fees()
    {
        $this->article->extra_fees()->createMany([
            [
                'fee_percent' => 15,
                'max_delay_days' => 7,
            ], [
                'fee_percent' => 30,
                'max_delay_days' => 15,
            ]
        ]);

        $this->assertCount(2, $this->article->extra_fees()->get());
    }
}
