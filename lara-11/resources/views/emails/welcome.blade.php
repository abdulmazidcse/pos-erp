@component('mail::message')
# Welcome, {{ $user->name }}!

Thank you for joining us at **{{ config('app.name') }}**. We are thrilled to have you on board!

We believe you'll love our platform and all the amazing features it offers. 

Here are a few things you can get started with:

- **Explore your dashboard**: Get an overview of all your activities.
- **Complete your profile**: Add details to get personalized recommendations.
- **Check out our guides**: Learn how to use the platform to its fullest.
 
Thanks again for joining, and we look forward to helping you succeed!

Best Regards,  
**The {{ config('app.name') }} Team**
 
@endcomponent
