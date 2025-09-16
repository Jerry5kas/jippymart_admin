@extends('layouts.app')

@section('title', 'SEO Management')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">SEO Management</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">SEO Management</li>
            </ol>
        </div>
    </div>
<div class="container-fluid">
        <div class="admin-top-section">
    <div class="row">
        <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/seo.png') }}" style="width: 32px; height: 32px;"></span>
                            <h3 class="mb-0">SEO Management</h3>
                            <span class="counter ml-3 seo_count">00</span>
                    </div>
                        <div class="d-flex top-title-right align-self-center">
                            <div class="select-box pl-3">
                                <button type="button" class="btn btn-info rounded-full" onclick="generateSitemap()">
                                    <i class="mdi mdi-sitemap mr-2"></i>Generate Sitemap
                            </button>
                        </div>
                            <div class="select-box pl-3">
                                <button type="button" class="btn btn-secondary rounded-full" onclick="previewSitemap()">
                                    <i class="mdi mdi-eye mr-2"></i>Preview Sitemap
                            </button>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sitemap Status Cards -->
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                                <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card card-box-with-icon bg--1">
                                        <div class="card-body d-flex justify-content-between align-items-center">
                                            <div class="card-box-with-content">
                                                <h4 class="text-dark-2 mb-1 h4 seo_pages_count">00</h4>
                                                <p class="mb-0 small text-dark-2">Total SEO Pages</p>
                                            </div>
                                            <span class="box-icon ab"><img src="{{ asset('images/seo_icon.png') }}" style="width: 32px; height: 32px;"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-box-with-icon bg--5">
                                        <div class="card-body d-flex justify-content-between align-items-center">
                                            <div class="card-box-with-content">
                                                <h4 class="text-dark-2 mb-1 h4 sitemap_urls">00</h4>
                                                <p class="mb-0 small text-dark-2">Sitemap URLs</p>
                                            </div>
                                            <span class="box-icon ab"><img src="{{ asset('images/sitemap_icon.png') }}" style="width: 32px; height: 32px;"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-box-with-icon bg--8">
                                        <div class="card-body d-flex justify-content-between align-items-center">
                                            <div class="card-box-with-content">
                                                <h4 class="text-dark-2 mb-1 h4 sitemap_size">-</h4>
                                                <p class="mb-0 small text-dark-2">Sitemap Size</p>
                                            </div>
                                            <span class="box-icon ab"><img src="{{ asset('images/file_size_icon.png') }}" style="width: 32px; height: 32px;"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-box-with-icon bg--6">
                                        <div class="card-body d-flex justify-content-between align-items-center">
                                            <div class="card-box-with-content">
                                                <h4 class="text-dark-2 mb-1 h4 last_modified">-</h4>
                                                <p class="mb-0 small text-dark-2">Last Modified</p>
                                            </div>
                                            <span class="box-icon ab"><img src="{{ asset('images/time_icon.png') }}" style="width: 32px; height: 32px;"></span>
                                        </div>
                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

                    <!-- SEO Pages Table -->
        <div class="table-list">
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">SEO Pages</h3>
                                <p class="mb-0 text-dark-2">Manage SEO settings for different pages</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a href="{{ route('seo.create') }}" class="btn-primary btn rounded-full">
                                        <i class="mdi mdi-plus mr-2"></i>Add New SEO Page
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="seoTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_array('seo.delete', json_decode(@session('user_permissions'), true))) { ?>
                                                <th class="delete-all">
                                                    <input type="checkbox" id="is_active">
                                                    <label class="col-3 control-label" for="is_active">
                                                        <a id="deleteAll" class="do_not_delete" href="javascript:void(0)">
                                                            <i class="mdi mdi-delete"></i> All
                                                        </a>
                                                    </label>
                                                </th>
                                            <?php } ?>
                                    <th>Page Key</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>OG Image</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                                    <tbody id="append_seo_pages"></tbody>
                        </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Settings Card -->
    <div class="row mt-4">
        <div class="col-12">
                <div class="card border">
                    <div class="card-header d-flex justify-content-between align-items-center border-0">
                        <div class="card-header-title">
                            <h3 class="text-dark-2 mb-2 h4">Global SEO Settings</h3>
                            <p class="mb-0 text-dark-2">Configure global SEO settings for your website</p>
                        </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('seo.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label for="site_name" class="control-label">Site Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" 
                                           value="{{ $settings['site_name'] ?? 'JippyMart' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label for="twitter_handle" class="control-label">Twitter Handle</label>
                                    <input type="text" class="form-control" id="twitter_handle" name="twitter_handle" 
                                           value="{{ $settings['twitter_handle'] ?? '' }}" 
                                           placeholder="@jippymart">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                                <label for="site_description" class="control-label">Site Description</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3" required>{{ $settings['site_description'] ?? '' }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label for="google_analytics_id" class="control-label">Google Analytics ID</label>
                                    <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id" 
                                           value="{{ $settings['google_analytics_id'] ?? '' }}" 
                                           placeholder="GA_MEASUREMENT_ID">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label for="contact_email" class="control-label">Contact Email</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                           value="{{ $settings['contact_email'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                                <label for="default_og_image" class="control-label">Default OG Image</label>
                            @if(isset($settings['default_og_image']) && $settings['default_og_image'])
                                <div class="mb-2">
                                    <img src="{{ asset($settings['default_og_image']) }}" alt="Current OG Image" 
                                         style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                                    <br>
                                    <small class="text-muted">Current image</small>
                                </div>
                            @endif
                            <input type="file" class="form-control-file" id="default_og_image" name="default_og_image" accept="image/*">
                            <small class="form-text text-muted">Recommended: 1200x630 pixels</small>
                        </div>

                            <button type="submit" class="btn btn-primary rounded-full">
                                <i class="mdi mdi-content-save mr-2"></i>Update Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if ($.inArray('seo.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }

function generateSitemap() {
    if (confirm('Generate sitemap now? This may take a few moments.')) {
        window.location.href = '{{ route("seo.generate-sitemap") }}';
    }
}

function previewSitemap() {
    window.open('{{ route("seo.preview-sitemap") }}', '_blank');
}

    $(document).ready(function () {
        // Load sitemap stats and update counters
        loadSitemapStats();
        
        const table = $('#seoTable').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: false,
            responsive: true,
            data: @json($pages),
            columns: [
                @if(in_array('seo.delete', json_decode(@session('user_permissions'), true)))
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return '<input type="checkbox" id="is_open_' + row.id + '" class="is_open" dataId="' + row.id + '"><label class="col-3 control-label" for="is_open_' + row.id + '"></label>';
                    }
                },
                @endif
                {
                    data: 'page_key',
                    render: function(data, type, row) {
                        var badge = row.page_key === 'default' ? '<span class="badge badge-info ml-2">Default</span>' : '';
                        return '<strong>' + data + '</strong>' + badge;
                    }
                },
                {
                    data: 'title',
                    render: function(data, type, row) {
                        return data ? data.substring(0, 50) + (data.length > 50 ? '...' : '') : '';
                    }
                },
                {
                    data: 'description',
                    render: function(data, type, row) {
                        return data ? data.substring(0, 80) + (data.length > 80 ? '...' : '') : '';
                    }
                },
                {
                    data: 'og_image',
                    orderable: false,
                    render: function(data, type, row) {
                        if (data) {
                            return '<img src="' + data + '" alt="OG Image" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">';
                        } else {
                            return '<span class="text-muted">No image</span>';
                        }
                    }
                },
                {
                    data: 'created_at',
                    render: function(data, type, row) {
                        return new Date(data).toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'short', 
                            day: 'numeric' 
                        });
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        var editUrl = '{{ route("seo.edit", ":id") }}'.replace(':id', row.id);
                        var deleteForm = '';
                        
                        if (row.page_key !== 'default') {
                            deleteForm = '<form action="{{ route("seo.destroy", ":id") }}" method="POST" style="display: inline-block;" onsubmit="return confirm(\'Are you sure you want to delete this SEO page?\')">' +
                                        '@csrf @method("DELETE")' +
                                        '<button type="submit" class="btn btn-sm btn-outline-danger ml-1"><i class="mdi mdi-delete"></i></button>' +
                                        '</form>';
                        }
                        
                        return '<span class="action-btn">' +
                               '<a href="' + editUrl + '"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>' +
                               deleteForm +
                               '</span>';
                    }
                }
            ],
            order: @if(in_array('seo.delete', json_decode(@session('user_permissions'), true))) [[1, 'asc']] @else [[0, 'asc']] @endif,
            columnDefs: [
                { orderable: false, targets: @if(in_array('seo.delete', json_decode(@session('user_permissions'), true))) [0, 6] @else [5] @endif },
            ],
            "language": {
                "zeroRecords": "No SEO pages found. Create your first one!",
                "emptyTable": "No SEO pages found. Create your first one!",
                "processing": ""
            },
            dom: 'lfr<"dataTables_export">tip',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="mdi mdi-cloud-download"></i> Export as',
                    className: 'btn btn-info',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Export Excel',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'excel', {
                                    columns: [
                                        { key: 'page_key', header: 'Page Key' },
                                        { key: 'title', header: 'Title' },
                                        { key: 'description', header: 'Description' },
                                        { key: 'created_at', header: 'Created At' },
                                    ],
                                    fileName: 'SEO Pages'
                                });
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'pdf', {
                                    columns: [
                                        { key: 'page_key', header: 'Page Key' },
                                        { key: 'title', header: 'Title' },
                                        { key: 'description', header: 'Description' },
                                        { key: 'created_at', header: 'Created At' },
                                    ],
                                    fileName: 'SEO Pages'
                                });
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'Export CSV',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'csv', {
                                    columns: [
                                        { key: 'page_key', header: 'Page Key' },
                                        { key: 'title', header: 'Title' },
                                        { key: 'description', header: 'Description' },
                                        { key: 'created_at', header: 'Created At' },
                                    ],
                                    fileName: 'SEO Pages'
                                });
                            }
                        }
                    ]
                }
            ],
            initComplete: function() {
                // Update counters
                var pageCount = table.data().count();
                $('.seo_count').text(pageCount);
                $('.seo_pages_count').text(pageCount);
                
                // Move export button to header area for cleaner UI
                $('.dataTables_export').detach().appendTo('.card-header-right');
                
                $('.dataTables_filter input').attr('placeholder', 'Search SEO pages...').attr('autocomplete','new-password').val('');
                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType === 3;
                }).remove();
                
                // Hide loading indicator
                jQuery("#data-table_processing").hide();
            }
        });

        // Delete all functionality
        $("#is_active").click(function () {
            $("#seoTable .is_open").prop('checked', $(this).prop('checked'));
        });

        $("#deleteAll").click(function () {
            if ($('#seoTable .is_open:checked').length) {
                if (confirm("Are you sure you want to delete selected SEO pages?")) {
                    $('#seoTable .is_open:checked').each(function () {
                        var dataId = $(this).attr('dataId');
                        // Add delete logic here
                        console.log('Delete SEO page:', dataId);
                    });
                    window.location.reload();
                }
            } else {
                alert("Please select at least one SEO page to delete.");
            }
        });
        
        // Add CSS for better button positioning
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                .card-header-right .dt-buttons {
                    margin-left: 10px;
                }
                .card-header-right .dt-buttons .btn {
                    margin-left: 5px;
                }
                .dataTables_wrapper .dataTables_paginate {
                    margin-top: 0;
                }
            `)
            .appendTo('head');
    });

    function loadSitemapStats() {
    fetch('{{ route("seo.sitemap-stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                    $('.sitemap_urls').text(data.url_count);
                    $('.sitemap_size').text(data.file_size_formatted);
                    $('.last_modified').text(data.last_modified);
            } else {
                    $('.sitemap_urls').text('0');
                    $('.sitemap_size').text('N/A');
                    $('.last_modified').text('Never');
            }
        })
        .catch(error => {
                console.error('Error loading sitemap stats:', error);
                $('.sitemap_urls').text('Error');
                $('.sitemap_size').text('Error');
                $('.last_modified').text('Error');
            });
    }
</script>
@endsection
