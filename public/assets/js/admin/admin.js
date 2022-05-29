$(document).ready(function() {
    // SideNav Button Initialization
    $('.button-collapse').sideNav({
        edge: 'left', // Choose the horizontal origin
        closeOnClick: false, // Closes side-nav on &lt;a&gt; clicks, useful for Angular/Meteor
        breakpoint: 1440, // Breakpoint for button collapse
        menuWidth: 250, // Width for sidenav
        timeDurationOpen: 300, // Time duration open menu
        timeDurationClose: 200, // Time duration open menu
        timeDurationOverlayOpen: 50, // Time duration open overlay
        timeDurationOverlayClose: 200, // Time duration close overlay
        easingOpen: 'easeOutQuad', // Open animation
        easingClose: 'easeOutCubic', // Close animation
        showOverlay: true, // Display overflay
        showCloseButton: false // Append close button into siednav
    });

    // SideNav Scrollbar Initialization
    var sideNavScrollbar = document.querySelector('.custom-scrollbar');
    var ps = new PerfectScrollbar(sideNavScrollbar);


    // Gestion des checkbox dans la liste
    let $principalCheckbox = $('#principal-checkbox'),
        $listCheckbook = $('.list-checkbook'),
        $listCheckbookLength = $listCheckbook.length,
        $listCheckbookNumber = 0,
        $btnBulkDelete = $('#entity-list-delete-bulk-btn'),
        $btnClassBulkDelete = $('.entity-list-delete-bulk-btn');

    $principalCheckbox.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $('.list-checkbook').prop('checked', true);

            $listCheckbookNumber = $listCheckbookLength;

            if ($listCheckbookLength > 0) {
                $btnBulkDelete.removeClass('d-none');
                $btnClassBulkDelete.removeClass('d-none');
            }

        } else {
            $('.list-checkbook').prop('checked', false);
            $btnBulkDelete.addClass('d-none');
            $btnClassBulkDelete.addClass('d-none');

            $listCheckbookNumber = 0;
        }
    });

    $listCheckbook.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $listCheckbookNumber++;
            $btnBulkDelete.removeClass('d-none');
            $btnClassBulkDelete.removeClass('d-none');

            if ($listCheckbookNumber === $listCheckbookLength)
                $principalCheckbox.prop('checked', true)
        } else {
            $listCheckbookNumber--;

            if ($listCheckbookNumber === 0) {
                $btnBulkDelete.addClass('d-none');
                $btnClassBulkDelete.addClass('d-none');
            }

            if ($listCheckbookNumber < $listCheckbookLength)
                $principalCheckbox.prop('checked', false)
        }
    });

    // Gestion des suppression
    let $container = $('#modal-container'),
        $checkbookContainer = $('#list-checkbook-container');

    // Achieve
    simpleModals($('.entity-achieve-delete'), 'app_admin_achieve_delete', $container);
    bulkModals($('.entity-achieve-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_achieve_bulk_delete', $container);

    // Category
    simpleModals($('.entity-category-delete'), 'app_admin_category_delete', $container);
    bulkModals($('.entity-category-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_category_bulk_delete', $container);

    // Banner
    simpleModals($('.entity-banner-delete'), 'app_admin_banner_delete', $container);
    bulkModals($('.entity-banner-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_banner_bulk_delete', $container);

    // Comment
    simpleModals($('.entity-comment-delete'), 'app_admin_comment_delete', $container);
    bulkModals($('.entity-comment-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_comment_bulk_delete', $container);

    // Customer
    simpleModals($('.entity-customer-delete'), 'app_admin_customer_delete', $container);
    bulkModals($('.entity-customer-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_customer_bulk_delete', $container);

    // Administrateur
    simpleModals($('.entity-admin-delete'), 'app_admin_admin_delete', $container);
    bulkModals($('.entity-admin-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_admin_bulk_delete', $container);

    // Demand
    simpleModals($('.entity-demand-delete'), 'app_admin_demand_delete', $container);
    bulkModals($('.entity-demand-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_demand_bulk_delete', $container);

    // Offer
    simpleModals($('.entity-offer-delete'), 'app_admin_offer_delete', $container);
    bulkModals($('.entity-offer-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_offer_bulk_delete', $container);

    // Post
    simpleModals($('.entity-post-delete'), 'app_admin_post_delete', $container);
    bulkModals($('.entity-post-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_post_bulk_delete', $container);

    // Reference
    simpleModals($('.entity-reference-delete'), 'app_admin_reference_delete', $container);
    bulkModals($('.entity-reference-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_reference_bulk_delete', $container);

    // Order
    simpleModals($('.entity-order-delete'), 'app_admin_order_delete', $container);
    bulkModals($('.entity-order-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_order_bulk_delete', $container);

    // Payment
    simpleModals($('.entity-payment-delete'), 'app_admin_payment_delete', $container);
    bulkModals($('.entity-payment-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_payment_bulk_delete', $container);

    // Taxe
    simpleModals($('.entity-tax-delete'), 'app_admin_taxe_delete', $container);
    bulkModals($('.entity-tax-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_taxe_bulk_delete', $container);

    // Review
    simpleModals($('.entity-review-delete'), 'app_admin_review_delete', $container);
    bulkModals($('.entity-review-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_review_bulk_delete', $container);

    // Service
    simpleModals($('.entity-service-delete'), 'app_admin_service_delete', $container);
    bulkModals($('.entity-service-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_service_bulk_delete', $container);

    // Team
    simpleModals($('.entity-team-delete'), 'app_admin_team_delete', $container);
    bulkModals($('.entity-team-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_team_bulk_delete', $container);
});




