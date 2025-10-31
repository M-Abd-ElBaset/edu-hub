<x-mail::message>
# 🎉 Congratulations {{ $userName }}!

You’ve successfully completed **{{ $courseTitle }}** on EduHub.

Your dedication and hard work have paid off!  
You can find your official course certificate attached to this email.

<x-mail::button :url="config('app.url') . '/courses'">
View More Courses
</x-mail::button>

Thanks for learning with us,  
**The EduHub Team**<br>
{{ config('app.name') }}
</x-mail::message>
