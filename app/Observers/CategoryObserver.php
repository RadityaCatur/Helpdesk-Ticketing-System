<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class CategoryObserver
{

    public static $observes = [
        Category::class
    ];

    /**
     * Handle the category "created" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
        activity()
            ->performedOn($category)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah membuat kategori applikasi baru dengan nama ' . $category->name);
    }

    /**
     * Handle the category "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        activity()
            ->performedOn($category)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah mengubah kategori applikasi dengan nama ' . $category->name);
    }

    /**
     * Handle the category "deleting" event.
     * 
     * @param   \App\Models\Category $category
     * @return  void
     */
    public function deleting(Category $category) {
        $category->projects()->each(function($project) {
            $project->category()->dissociate();
            $project->save();
        });
    }

    /**
     * Handle the category "deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        activity()
            ->performedOn($category)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah menghapus kategori applikasi dengan nama ' . $category->name);        
    }

    /**
     * Handle the category "restored" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        //
    }

    /**
     * Handle the category "force deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        //
    }
}
