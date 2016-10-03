# Videouri

![Videouri](https://i.imgur.com/Ys6Asfc.png)

## What is it
It is a video search engine, with a synchronous search system for content from Youtube, Dailymotion and Vimeo. On the
back-end is backed by:
 - [LEMP](https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-on-ubuntu-14-04)
 - [Laravel](https://laravel.com/)
 - [REDIS](http://redis.io/) - For caching and job queues
 - [Supervisor](http://supervisord.org/)
 
And on the front-end:
 - [VueJS](https://vuejs.org/)
 - [jQuery](https://jquery.com/)
 - [MaterializeCSS](http://materializecss.com/)
 - [Isotope](http://isotope.metafizzy.co/)
 - [ImagesLoaded](http://imagesloaded.desandro.com/)
 - [VideoJS](http://videojs.com/) - along with plugins to support playback for Youtube, Dailymotion and Vimeo
 - [Linkify](https://github.com/SoapBox/linkifyjs)
 - [Readmore.JS](http://jedfoster.com/Readmore.js/)
 
## Why did you do it
<b>To learn!</b> I started this project back around 2011 as I saw it as a perfect opportunity to learn by
trying to build an idea that I had and so getting into programming.

## How did you do it
It all started with CodeIgniter 2 (popular framework back then, and it seemed as a good platform to learn and start the project),
along with [jQuery](https://blog.jquery.com/2011/02/24/jquery-151-released/).

No database was involved at first, and the server layout was a basic Ubuntu serving Apache and PHP.

## Notes
I spent a lot of time on this project, but I have also learned so much out of it and helped me even find work.
I've learned the importance of caching and its pitfalls when its not done right, ending up with memory leaks; I've learned 
about SEO and what it means to be penalized by google; it has helped learn how to efficiently consume APIs and avoid
as much as possible rate limitings and so many other valuable lessons over the countless days and nights I spent trying
figure things out. But in the end, it was worth it.
