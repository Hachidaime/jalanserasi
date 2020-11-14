<div class="row">
    <div class="col-md-4">
        <div>
            <p class="text-justify">
                <b>Selamat datang</b> di website Sistem Jaringan Jalan Kabupaten Semarang, website ini didesain sebagai penguatan database dan survey kondisi jalan dalam bentuk visualisasi GIS (Geographic Information System) dalam membantu Dinas Pekerjaan Umum Kabupaten Semarang, Bidang Bina Marga sebagai Tupoksi Bidang Pembangunan, Pemeliharaan Jalan dan Jembatan.<br><br>
				Kami sadari bahwa di dalam website ini akan ditemukan berbagai kekurangan mengingat arus informasi senantiasa bergerak dinamis. Namun kami akan senantiasa melakukan perbaikan dan pengembangan secara bertahap demi peningkatan mutu informasi yang kami sajikan. Semoga informasi yang terkadung di dalamnya memberi nilai manfaat yang nyata bagi masyarakat.<br> <br>
                Terima kasih telah mengunjungi website kami sekaligus mengapresiasi apa yang telah kami lakukan.
            </p>
        </div>
    </div>
    <div class="col-md-8">
        <img src="{$smarty.const.SERVER_BASE}/assets/img/bg-main.png?t={$smarty.now}" class="img-fluid mb-3" >
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        {foreach from=$smarty.const.SITE_INFO key=k item=v}
        {if $k eq 'Alamat'}
        <p class="text-justify">
            <strong>{$k}</strong>: {$v}
        </p>
        {else}
        <div class="row">
            <div class="col-md-3 pr-0"><strong>{$k}<span class="float-lg-right float-md-right float-sm-none">:</span></strong></div>
            <div class="col-md-9">{$v}</div>
        </div>
        {/if}
        {/foreach}
    </div>
    <div class="col-md-8">
        <div id="demo" class="carousel slide" data-ride="carousel">

            <!-- Indicators 
            <ul class="carousel-indicators">
            <li data-target="#demo" data-slide-to="0" class="active"></li>
            <li data-target="#demo" data-slide-to="1"></li>
            <li data-target="#demo" data-slide-to="2"></li>
            <li data-target="#demo" data-slide-to="3"></li>
            </ul>
            -->

            <!-- The slideshow -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{$smarty.const.SERVER_BASE}/assets/img/Slide1.png?t={$smarty.now}" alt="Bupati" class="img-fluid">
                </div>
                <div class="carousel-item">
                    <img src="{$smarty.const.SERVER_BASE}/assets/img/Slide2.png?t={$smarty.now}" alt="TNI" class="img-fluid">
                </div>
                <div class="carousel-item">
                    <img src="{$smarty.const.SERVER_BASE}/assets/img/Slide3.png?t={$smarty.now}" alt="Jalan" class="img-fluid">
                </div>
                <div class="carousel-item">
                    <img src="{$smarty.const.SERVER_BASE}/assets/img/Slide4.png?t={$smarty.now}" alt="Gedong9" class="img-fluid">
                </div>
				<div class="carousel-item">
                    <img src="{$smarty.const.SERVER_BASE}/assets/img/Slide5.png?t={$smarty.now}" alt="ElingBening - RawaPening" class="img-fluid">
                </div>
            </div>

            <!-- Left and right controls -->
            <a class="carousel-control-prev" href="#demo" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#demo" data-slide="next">
                <span class="carousel-control-next-icon"></span>
            </a>
        </div>
        <!-- <img src="https://dummyimage.com/1024x200/000/fff.png&text=Lorem+ipsum" class="img-fluid" > -->
    </div>
</div>