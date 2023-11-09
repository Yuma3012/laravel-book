<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\BookRequest;
use Illuminate\Validation\Rule;
use Validator;

class BookController extends Controller
{
    public function index(Request $request)
    {
        //        $books = Book::all();
        //        $books = Book::paginate(10);
        //
        //        return view('book.index', ['books' => $books]);

        $input = $request->only('name', 'status', 'author', 'publication', 'note');
        $books = Book::search($input)->orderBy('id', 'desc')->paginate(10);
        //        $books = Book::paginate(10);

        $publications = Book::select('publication')->groupBy('publication')->pluck('publication');
        $authors = Book::select('author')->groupBy('author')->pluck('author');
        //        $statuses = Book::select('status')->groupBy('status')->pluck('status');

        return view(
            'book.index',
            [
                'books' => $books,
                // selectboxの値
                'publications' => $publications,
                'authors' => $authors,
                //                'statuses' => $statuses,

                // 検索する値
                'name' => $input['name'] ?? '',
                'publication' => $input['publication'] ?? '',
                'author' => $input['author'] ?? '',
                'status' => $input['status'] ?? '',
                'note' => $input['note'] ?? '',
            ]
        );
    }

    public function detail($id)
    {
        $book = Book::findOrFail($id);
        return view('book.detail', [
            'book' => $book,
        ]);
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        return view('book.edit', [
            'book' => $book,
        ]);
    }

    public function update(BookRequest $request)
    {
        try {
            DB::beginTransaction();
            $book = Book::find($request->input('id'));
            $book->name = $request->input('name');
            $book->status = $request->input('status');
            $book->author = $request->input('author');
            $book->publication = $request->input('publication');
            $book->read_at = $request->input('read_at');
            $book->note = $request->input('note');
            $book->save();
            DB::commit();
            return redirect('book')->with('status', '本を更新しました。');
        } catch (\Exception $ex) {
            DB::rollback();
            logger($ex->getMessage());
            return redirect('book')->withErrors($ex->getMessage());
        }
    }

    public function new()
    {
        return view('book.new');
    }

    public function create(BookRequest $request)
    {
        try {
            Book::create($request->all());
            return redirect('book')->with('status', '本を作成しました。');
        } catch (\Exception $ex) {
            logger($ex->getMessage());
            return redirect('book')->withErrors($ex->getMessage());
        }
    }

    public function remove($id)
    {
        try {
            Book::findOrFail($id)->delete();
            return redirect('book')->with('status', '本を削除しました。');
        } catch (\Exception $ex) {
            logger($ex->getMessage());
            abort(404);
            // return redirect('book')->withErrors($ex->getMessage());
        }
    }
}
