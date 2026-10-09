<x-error-page code="403" :heading="__('Access denied')" :message="$exception?->getMessage() ?: __('You do not have permission to view this page.')" />
