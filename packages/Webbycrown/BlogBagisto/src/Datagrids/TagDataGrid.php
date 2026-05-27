<?php

namespace Webbycrown\BlogBagisto\Datagrids;

use Illuminate\Support\Facades\DB;
use Webkul\Core\Models\Channel;
use Webkul\DataGrid\DataGrid;

class TagDataGrid extends DataGrid
{
    /**
     * Set index columns, ex: id.
     *
     * @var int
     */
    protected $index = 'id';

    /**
     * Default sort order of datagrid.
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Locale.
     *
     * @var string
     */
    protected $locale = 'all';

    /**
     * Channel.
     *
     * @var string
     */
    protected $channel = 'all';

    /**
     * Contains the keys for which extra filters to render.
     *
     * @var string[]
     */
    protected $extraFilters = [
        'channels',
        'locales',
    ];

    public function prepareQueryBuilder()
    {
        $locale = core()->getRequestedLocaleCode();

        $queryBuilder = DB::table('blog_tags')
            ->leftJoin('blog_tag_translations', function ($join) use ($locale) {
                $join->on('blog_tags.id', '=', 'blog_tag_translations.blog_tag_id')
                    ->where('blog_tag_translations.locale', $locale);
            })
            ->select(
                'blog_tags.id',
                'blog_tags.status',
                'blog_tag_translations.name',
                'blog_tag_translations.slug',
            );

        return $queryBuilder;
    }

    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('blog::app.datagrid.id'),
            'type'       => 'integer',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('blog::app.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('blog::app.datagrid.status'),
            'type'       => 'boolean',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($value) {
                if ($value->status == 1) {
                    return '<span class="badge badge-md badge-success label-active">' . trans('blog::app.tag.status-true') . '</span>';
                } else {
                    return '<span class="badge badge-md badge-danger label-info">' . trans('blog::app.tag.status-false') . '</span>';
                }
            },
        ]);
    }

    public function prepareActions()
    {
        if (bouncer()->hasPermission('blog.tag.edit')) {
            $this->addAction([
                'title' => 'edit',
                'method' => 'GET',
                'route' => 'admin.blog.tag.edit',
                'icon' => 'icon-edit',
                'url'    => function ($row) {
                    return route('admin.blog.tag.edit', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('blog.tag.delete')) {
            $this->addAction([
                'title' => 'delete',
                'method' => 'POST',
                'route' => 'admin.blog.tag.delete',
                'icon' => 'icon-delete',
                'url'    => function ($row) {
                    return route('admin.blog.tag.delete', $row->id);
                },
            ]);
        }
    }

    public function prepareMassActions()
    {
        if (bouncer()->hasPermission('blog.tag.delete')) {
            $this->addMassAction([
                'type'   => 'delete',
                'label'  => trans('admin::app.datagrid.delete'),
                'title'  => 'Delete',
                'action' => route('admin.blog.tag.massdelete'),
                'url' => route('admin.blog.tag.massdelete'),
                'method' => 'POST',
            ]);
        }
    }
}
