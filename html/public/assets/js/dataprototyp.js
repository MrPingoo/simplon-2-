(function ($) {
    $.fn.collection = function (options) {
        var defaults = {
            itemSelector: '.item',
            addSelector: '.add-item',
            removeSelector: '.remove-item',
            onAdd: function ($item) {
            },
            onRemove: function () {
            }
        };
        var index = this.children().length;
        return this.each(function () {

            var $this = $(this);
            var settings = $.extend(true, {}, defaults, options, $this.data());

            function addAddListener()
            {

                $(settings.addSelector).on('click', function (e) {
                    var $item = addCollectionItem();
                    settings.onAdd($item);
                    e.preventDefault();
                });
            }
            ;
            function addRemoveListener()
            {
                $this.find(settings.removeSelector).on('click', function (e) {
                    $(this).parents(settings.itemSelector).remove();
                    settings.onRemove();
                    e.preventDefault();
                });
            }
            ;
            function addCollectionItem()
            {
                var item = $($this.attr('data-prototype').replace(/__name__/g, index));
                index += 1;
                $this.append(item);
                addRemoveListener();
                return item;
            }
            ;
            addAddListener();
            addRemoveListener();
        });
    };
}(jQuery));