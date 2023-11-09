<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Factories\UserFactory;
use App\Models\User;
use App\Models\Book;
class BookTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }



    //     1.ログインユーザがbook.indexにアクセスできるか？

    /**
     * テスト名について
     * ・接頭辞にtest_をつける
     * ・@testをつける。メソッド名にtestは不要
     * ・日本語名でも大丈夫
     * test_<url>_<証明する内容>
     */

    // ログインしてないユーザがbook.indexにアクセスできないこと(302)
    public function test_book_index_ng()
    {
        $response = $this->get('/book');
        $response->assertStatus(302);
    }

    // ログインユーザがbook.indexにアクセスできること(200)
    public function test_book_index_ok()
    {
        // ログインさせる場合は、factoryでuserを作り、actingAsでリクエストする。
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/book');
        $response->assertStatus(200);
    }

    // 存在するIDでbook.detailにアクセスできることを確認（200）
    public function test_book_detail_id_exist()
    {
        // ログインさせる場合は、factoryでuserを作り、actingAsでリクエストする。
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->get("/book/detail/$book->id");
        $response->assertStatus(200);
    }

    // 存在しないIDでbook.detailにアクセスできないことを確認（404）
    public function test_book_detail_id_not_exist()
    {
        // ログインさせる場合は、factoryでuserを作り、actingAsでリクエストする。
        $user = User::factory()->create();
        //        $book = Book::factory()->create();

        $response = $this->actingAs($user)->get("/book/detail/9999");
        $response->assertStatus(404);
    }

    // 存在するIDでbook.editにアクセスできることを確認（200）
    public function test_book_edit_id_exist()
    {
        // ログインさせる場合は、factoryでuserを作り、actingAsでリクエストする。
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->get("/book/edit/$book->id");
        $response->assertStatus(200);
    }

    // 存在しないIDでbook.editにアクセスできないことを確認
    public function test_book_edit_id_not_exist()
    {
        // ログインさせる場合は、factoryでuserを作り、actingAsでリクエストする。
        $user = User::factory()->create();
        //        $book = Book::factory()->create();

        $response = $this->actingAs($user)->get("/book/edit/9999");
        $response->assertStatus(404);
    }

    // book.editで更新処理が正常に行えること
    public function test_book_update_ok()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $params = [
            'id' => $book->id,
            'name' => 'test',
            'status' => 1,
            'author' => 'test',
            'publication' => 'test',
            'read_at' => '2022-10-01',
            'note' => 'test',
        ];

        $response = $this->actingAs($user)->patch('/book/update', $params);
        $response->assertStatus(302); // httpステータスが302を返すこと
        $response->assertSessionHas('status', '本を更新しました。'); // セッションにstatusが含まれていて、値が「本を更新しました。」となっていること
        $this->assertDatabaseHas('books', $params); // dbの値が更新されたこと
    }

    // // 不正な値でbook.editで更新処理がエラーになること
    // public function test_book_update_status_ng()
    // {
    //     $user = User::factory()->create();
    //     $book = Book::factory()->create();

    //     $params = [
    //         'id' => $book->id,
    //         'name' => 'test',
    //         'status' => 9, // 不正な値
    //         'author' => 'test',
    //         'publication' => 'test',
    //         'read_at' => '2022-10-01x',
    //         'note' => 'test',
    //     ];

    //     $response = $this->actingAs($user)->patch('/book/update', $params);
    //     $response->assertStatus(302);
    //     $response->assertSessionHasErrors(['status' => '選択されたステータスは、有効ではありません。']); // エラーセッションに値が含まれること
    //     $this->assertDatabaseMissing('books', $params); // dbの値が更新されてないこと
    // }

    // // 不正な値でbook.editで更新処理がエラーになること（複数）
    // public function test_book_update_status_ng_all()
    // {
    //     $user = User::factory()->create();
    //     $book = Book::factory()->create();

    //     $params = [
    //         'id' => $book->id,
    //         'name' => $this->faker->realText(256), // 不正な値
    //         'status' => 9, // 不正な値
    //         'author' => $this->faker->realText(256), // 不正な値
    //         'publication' => $this->faker->realText(256), // 不正な値
    //         'read_at' => '2022-10-01', // 不正な値
    //         'note' => $this->faker->realText(1001), // 不正な値
    //     ];

    //     $response = $this->actingAs($user)->patch('/book/update', $params);
    //     $response->assertStatus(302);
    //     $this->assertDatabaseMissing('books', $params); // dbの値が更新されてないこと
    //     $response->assertInvalid(['name' => '名前は、255文字以下にしてください。']);
    //     $response->assertInvalid(['status' => '選択されたステータスは、有効ではありません。']);
    //     $response->assertInvalid(['author' => '著者は、255文字以下にしてください。']);
    //     $response->assertInvalid(['publication' => '出版は、255文字以下にしてください。']);
    //     $response->assertInvalid(['read_at' => '読破日は、正しい日付ではありません。']);
    //     $response->assertInvalid(['note' => 'メモは、1000文字以下にしてください。']);

    //     /**
    //      * ・エラーメッセージ
    //      * name: 名前は、255文字以下にしてください。
    //      * status: 選択されたステータスは、有効ではありません。
    //      * author: 著者は、255文字以下にしてください。
    //      * publication: 出版は、255文字以下にしてください。
    //      * read_at: 読み終わった日は、正しい日付ではありません。-> 読破日に変える（バグってる
    //      * note: メモは、1000文字以下にしてください。
    //      */
    // }

    // book.newで更新処理が正常に行えること
    public function test_book_create_ok()
    {
        $user = User::factory()->create();

        $params = [
            'name' => 'test',
            'status' => 1,
            'author' => 'test',
            'publication' => 'test',
            'read_at' => '2022-10-01',
            'note' => 'test',
        ];

        $response = $this->actingAs($user)->post('/book/create', $params);
        $response->assertStatus(302); // httpステータスが302を返すこと
        $response->assertSessionHas('status', '本を作成しました。'); // セッションにstatusが含まれていて、値が「本を作成しました。」となっていること
        $this->assertDatabaseHas('books', $params); // dbの値が更新されたこと
    }

    // // 不正な値でbook.newで更新処理がエラーになること
    // public function test_book_create_status_ng_all()
    // {
    //     $user = User::factory()->create();
    //     //        $book = Book::factory()->create();

    //     $params = [
    //         'name' => $this->faker->realText(256), // 不正な値
    //         'status' => 9, // 不正な値
    //         'author' => $this->faker->realText(256), // 不正な値
    //         'publication' => $this->faker->realText(256), // 不正な値
    //         'read_at' => '2022-10-01xxxx', // 不正な値
    //         'note' => $this->faker->realText(1001), // 不正な値
    //     ];

    //     $response = $this->actingAs($user)->post('/book/create', $params);
    //     $response->assertStatus(302);
    //     $this->assertDatabaseMissing('books', $params); // dbの値が更新されてないこと
    //     $response->assertInvalid(['name' => '名前は、255文字以下にしてください。']);
    //     $response->assertInvalid(['status' => '選択されたステータスは、有効ではありません。']);
    //     $response->assertInvalid(['author' => '著者は、255文字以下にしてください。']);
    //     $response->assertInvalid(['publication' => '出版は、255文字以下にしてください。']);
    //     $response->assertInvalid(['read_at' => '読破日は、正しい日付ではありません。']);
    //     $response->assertInvalid(['note' => 'メモは、1000文字以下にしてください。']);

    //     /**
    //      * ・エラーメッセージ
    //      * name: 名前は、255文字以下にしてください。
    //      * status: 選択されたステータスは、有効ではありません。
    //      * author: 著者は、255文字以下にしてください。
    //      * publication: 出版は、255文字以下にしてください。
    //      * read_at: 読み終わった日は、正しい日付ではありません。-> 読破日に変える（バグってる
    //      * note: メモは、1000文字以下にしてください。
    //      */
    // }

    // book.removeで更新処理が正常に行えること
    public function test_book_remove_ok()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->delete("/book/remove/$book->id");
        $response->assertStatus(302); // httpステータスが302を返すこと
        $response->assertSessionHas('status', '本を削除しました。'); // セッションにstatusが含まれていて、値が「本を削除しました。」となっていること

        $book = Book::find($book->id);
        $this->assertEmpty($book); // NOTE: $thisはUnitTestの関数
    }

    // 不正な値でbook.removeで更新処理がエラーになること
    public function test_book_remove_ng()
    {
        $user = User::factory()->create();
        Book::factory()->create();

        $response = $this->actingAs($user)->delete("/book/remove/99999");

        $response->assertStatus(404);
    }
// 2.ログインしてないユーザがbook.indexにアクセスできないかか？
// 3.book.detailにアクセスできるか？
// 4.存在しない値でbook.detailにアクセス出来ないことを確認する
// 5.book.editにアクセスできるか？
// 6.存在しない値でbook.editにアクセス出来ないことを確認する
// 7.book.editで正常系のテストを実施する
// 8.book.editで異常系のテストを実施する
// 9.book.newで正常系のテストを実施する
// 10.book.newで異常系のテストを実施する
// 11.removeで正常系のテストを実施する
// 12.removeで異常系のテストを実施する
// 13.検索機能が正常にテスト出来ること
}
