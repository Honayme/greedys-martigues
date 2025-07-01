@component('shop::emails.layout')

    {{-- Greeting --}}
    <div style="margin-bottom: 20px;">
        <p style="font-size: 18px; font-weight: 600; color: #121A26; line-height: 24px;">
            @lang('shop::app.emails.dear', ['customer_name' => $comment->order->customer_full_name]), 👋
        </p>

        <p style="font-size: 16px; color: #5E5E5E; line-height: 24px;">
            @lang('shop::app.emails.orders.commented.title', [
                'order_id'   => '<a href="' . route('shop.customers.account.orders.view', $comment->order_id) . '" style="color: #2969FF; text-decoration: underline;">#' . $comment->order->increment_id . '</a>',
                'created_at' => core()->formatDate($comment->order->created_at, 'Y-m-d H:i:s')
            ])
        </p>
    </div>

    {{-- Comment Box --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F9FAFB; border: 1px solid #E2E8F0; border-radius: 4px; margin-bottom: 20px;">
        <tr>
            <td style="padding: 20px;">
                <p style="font-size: 16px; color: #384860; line-height: 24px; margin: 0;">
                    {{ $comment->comment }}
                </p>
            </td>
        </tr>
    </table>

@endcomponent
