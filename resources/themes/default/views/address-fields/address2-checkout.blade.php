<!-- Validation douce : Warning si adresse principale trop longue -->
<p
    v-if="address.address && address.address[0] && address.address[0].length > 38"
    class="mb-4 text-xs text-orange-500"
>
    ⚠️ @lang('shop::app.checkout.onepage.address.address-warning')
</p>

<!-- Address 2 (Complément d'adresse) - Checkout -->
<x-shop::form.control-group>
    <x-shop::form.control-group.label class="!mt-0">
        @lang('shop::app.checkout.onepage.address.address2')
    </x-shop::form.control-group.label>

    <x-shop::form.control-group.control
        type="text"
        ::name="controlName + '.address2'"
        ::value="address.address2"
        rules="address"
        :label="trans('shop::app.checkout.onepage.address.address2')"
        :placeholder="trans('shop::app.checkout.onepage.address.address2')"
    />

    <x-shop::form.control-group.error ::name="controlName + '.address2'" />
</x-shop::form.control-group>
