@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Create Mart Banner Item</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{!! route('mart.banners') !!}">Mart Banner Items</a></li>
                <li class="breadcrumb-item active">Create Banner</li>
            </ol>
        </div>
    </div>
    <div class="card-body">
        <div class="error_top" style="display:none"></div>
        <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">
                <fieldset>
                    <legend>Mart Banner Item Details</legend>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">Title *</label>
                        <div class="col-7">
                            <input type="text" class="form-control title" placeholder="Enter banner title">
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">Description</label>
                        <div class="col-7">
                            <textarea class="form-control description" rows="3" placeholder="Enter banner description"></textarea>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">Text (Optional)</label>
                        <div class="col-7">
                            <textarea class="form-control text" rows="2" placeholder="Enter additional text (optional)"></textarea>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">Order</label>
                        <div class="col-7">
                            <input type="number" class="form-control set_order" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <div class="form-check width-100">
                            <input type="checkbox" id="is_publish" checked>
                            <label class="col-3 control-label" for="is_publish">Publish</label>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">Photo</label>
                        <input type="file" onChange="handleFileSelect(event)" class="col-7">
                        <div id="uploding_image"></div>
                        <div class="placeholder_img_thumb user_image"></div>
                    </div>
                    <div class="form-group row width-50" id="banner_position">
                        <label class="col-3 control-label">Position</label>
                        <div class="col-7">
                            <select name="position" id="position" class="form-control">
                                <option value="top">Top</option>
                                <option value="middle">Middle</option>
                                <option value="bottom">Bottom</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-100 radio-form-row d-flex" id="redirect_type_div">
                        <div class="radio-form col-md-2">
                            <input type="radio" class="redirect_type" value="store" name="redirect_type" id="store">
                            <label class="custom-control-label">Store</label>
                        </div>
                        <div class="radio-form col-md-2">
                            <input type="radio" class="redirect_type" value="product" name="redirect_type" id="product">
                            <label class="custom-control-label">Product</label>
                        </div>
                        <div class="radio-form col-md-4">
                            <input type="radio" class="redirect_type" value="external_link" name="redirect_type" id="external" checked>
                            <label class="custom-control-label">External Link</label>
                        </div>
                    </div>
                    <div class="form-group row width-50" id="vendor_div" style="display: none;">
                        <label class="col-3 control-label">Store</label>
                        <div class="col-7">
                            <select name="storeId" id="storeId" class="form-control">
                                <option value="">Select Store</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-50" id="product_div" style="display: none;">
                        <label class="col-3 control-label">Product</label>
                        <div class="col-7">
                            <select name="productId" id="productId" class="form-control">
                                <option value="">Select Product</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-100" id="external_link_div">
                        <label class="col-3 control-label">External Link</label>
                        <div class="col-7">
                            <input type="text" class="form-control extlink" id="external_link" placeholder="https://example.com">
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="form-group col-12 text-center">
        <button type="button" class="btn btn-primary save-mart-banner-btn"><i class="fa fa-save"></i> Save</button>
        <a href="{!! route('mart.banners') !!}" class="btn btn-default"><i class="fa fa-undo"></i>Cancel</a>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load toastr library -->
<script src="{{ asset('js/toastr.js') }}"></script>

<script>
    var database = firebase.firestore();
    var storage = firebase.storage();
    var photo = '';
    var fileName = '';
    var storageRef = firebase.storage().ref('images');

    $(document).ready(function() {
        // Load stores for store redirect
        loadStores();
        
        // Load products for product redirect
        loadProducts();

        // Handle redirect type change
        $('.redirect_type').on('change', function() {
            var redirectType = $(this).val();
            $('#vendor_div, #product_div, #external_link_div').hide();
            
            if (redirectType === 'store') {
                $('#vendor_div').show();
            } else if (redirectType === 'product') {
                $('#product_div').show();
            } else if (redirectType === 'external_link') {
                $('#external_link_div').show();
            }
        });

        // Handle save button click
        $('.save-mart-banner-btn').on('click', function() {
            saveMartBanner();
        });
    });

    // Load stores for store redirect
    function loadStores() {
        $('#storeId').html("");
        $('#storeId').append($("<option value=''>Select Store</option>"));
        
        var ref_vendors = database.collection('vendors');
        ref_vendors.get().then(async function(snapshots) {
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                $('#storeId').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.title));
            });
        }).catch(function(error) {
            console.error('Error loading stores:', error);
        });
    }

    // Load products for product redirect
    function loadProducts() {
        $('#productId').html("");
        $('#productId').append($("<option value=''>Select Product</option>"));
        
        var ref_products = database.collection('mart_items');
        ref_products.get().then(async function(snapshots) {
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                $('#productId').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.name));
            });
        }).catch(function(error) {
            console.error('Error loading products:', error);
        });
    }

    function handleFileSelect(evt) {
        var f = evt.target.files[0];
        var reader = new FileReader();
        reader.onload = (function(theFile) {
            return function(e) {
                var filePayload = e.target.result;
                var val = f.name;
                var ext = val.split('.')[1];
                var docName = val.split('fakepath')[1];
                var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                photo = filePayload;
                fileName = filename;
                $(".user_image").empty();
                $(".user_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');
            };
        })(f);
        reader.readAsDataURL(f);
    }

    async function storeImageData() {
        var newPhoto = '';
        try {
            photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")
            var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', {
                contentType: 'image/jpg'
            });
            var downloadURL = await uploadTask.ref.getDownloadURL();
            newPhoto = downloadURL;
            photo = downloadURL;
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }

    async function saveMartBanner() {
        // Get form values
        var title = $('.title').val().trim();
        var description = $('.description').val().trim();
        var text = $('.text').val().trim();
        var setOrder = parseInt($('.set_order').val()) || 0;
        var isPublish = $('#is_publish').is(':checked');
        var position = $('#position').val();
        var redirectType = $('.redirect_type:checked').val();
        var storeId = $('#storeId').val();
        var productId = $('#productId').val();
        var externalLink = $('#external_link').val();

        // Validation
        if (!title) {
            $('.error_top').show().html('<p>Please enter banner title</p>');
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please enter banner title');
            }
            return;
        }

        if (!photo) {
            $('.error_top').show().html('<p>Please select a banner image</p>');
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please select a banner image');
            }
            return;
        }

        if (!redirectType) {
            $('.error_top').show().html('<p>Please select a redirect type</p>');
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please select a redirect type');
            }
            return;
        }

        // Validate redirect specific fields
        if (redirectType === 'store' && !storeId) {
            $('.error_top').show().html('<p>Please select a store</p>');
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please select a store');
            }
            return;
        }

        if (redirectType === 'product' && !productId) {
            $('.error_top').show().html('<p>Please select a product</p>');
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please select a product');
            }
            return;
        }

        if (redirectType === 'external_link' && !externalLink) {
            $('.error_top').show().html('<p>Please enter external link</p>');
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please enter external link');
            }
            return;
        }

        // Clear previous errors
        $('.error_top').hide();

        // Disable save button and show loading
        $('.save-mart-banner-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        try {
            // Upload image first
            const imageUrl = await storeImageData();
            
            // Prepare banner data
            const bannerData = {
                title: title,
                description: description,
                text: text,
                set_order: setOrder,
                is_publish: isPublish,
                position: position,
                redirect_type: redirectType,
                photo: imageUrl,
                created_at: firebase.firestore.FieldValue.serverTimestamp(),
                updated_at: firebase.firestore.FieldValue.serverTimestamp()
            };

            // Add redirect specific data
            if (redirectType === 'store') {
                bannerData.storeId = storeId;
            } else if (redirectType === 'product') {
                bannerData.productId = productId;
            } else if (redirectType === 'external_link') {
                bannerData.external_link = externalLink;
            }

            // Save to Firestore
            const docRef = await database.collection('mart_banners').add(bannerData);
            
            // Success - redirect to index page
            if (typeof toastr !== 'undefined') {
                toastr.success('Banner created successfully!');
            }
            
            setTimeout(() => {
                window.location.href = '{{ route("mart.banners") }}';
            }, 1000);

        } catch (error) {
            console.error('Error saving banner:', error);
            $('.error_top').show().html('<p>Error saving banner: ' + error.message + '</p>');
            $('.save-mart-banner-btn').prop('disabled', false).html('<i class="fa fa-save"></i> Save');
            // Also show toastr error
            if (typeof toastr !== 'undefined') {
                toastr.error('Error saving banner: ' + error.message);
            }
        }
    }
</script>
@endsection
