<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Dokumen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
        integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .box-content {
            height: calc(100vh - 130px);
            margin-bottom: 4px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
        }

        img.zoom-25 {
            width: 25%;
        }

        img.zoom-50 {
            width: 50%;
        }

        img.zoom-75 {
            width: 75%;
        }

        img.zoom-100 {
            width: 100%;
        }

        img.zoom-150 {
            width: 150%;
        }

        img.zoom-200 {
            width: 200%;
        }

        img.zoom-300 {
            width: 300%;
        }

        .zoom-disabled {
            pointer-events: none;
            opacity: 0.4;
        }

        .img {
            overflow: auto;
            width: 100%;
            text-align: center;
        }

        .iframe {
            width: 100%;
            height: calc(100vh - 136px);
        }

        .box-header {
            padding: 18px;
            font-size: 32px;
            font-weight: bold;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>
</head>

<body>
    <div id="app">
        <div class="box-header">
            @{{ formatLabel(data[role].type) }} (@{{ role + 1 }}/@{{ data.length }})
        </div>
        <div class="box-content">
            <div class="img">
                <template v-if="isPdf(data[role])">
                    <iframe class="iframe" :src="data[role].url"></iframe>
                </template>
                <template v-if="isImage(data[role])">
                    <img :src="data[role].url" :class="zoom" alt="">
                </template>
                <template v-if="isOther(data[role])">
                    <button class="btn btn-lg btn-primary" v-on:click="openManual(data[role])">BUKA MANUAL</button>
                </template>
            </div>
        </div>
        <div class="d-flex justify-content-center gap-1">
            <button class="btn btn-secondary" v-on:click="pprev()">
                <i class="fas fa-angle-double-left"></i>
            </button>
            <button class="btn btn-secondary" v-on:click="prev()">
                <i class="fas fa-angle-left"></i>
            </button>

            <button class="btn btn-secondary" v-on:click="next()">
                <i class="fas fa-angle-right"></i>
            </button>
            <button class="btn btn-secondary" v-on:click="nnext()">
                <i class="fas fa-angle-double-right"></i>
            </button>
            <div class="dropdown dropup" :class="{ 'zoom-disabled': isPdf(data[role]) }">
                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Zoom
                </a>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" v-on:click="setZoom('original')" href="javascript:void(0)">Original</a>
                    </li>
                    <li><a class="dropdown-item" v-on:click="setZoom('25')" href="javascript:void(0)">25%</a></li>
                    <li><a class="dropdown-item" v-on:click="setZoom('50')" href="javascript:void(0)">50%</a></li>
                    <li><a class="dropdown-item" v-on:click="setZoom('75')" href="javascript:void(0)">75%</a></li>
                    <li><a class="dropdown-item" v-on:click="setZoom('100')" href="javascript:void(0)">100%</a></li>
                    <li><a class="dropdown-item" v-on:click="setZoom('150')" href="javascript:void(0)">150%</a></li>
                    <li><a class="dropdown-item" v-on:click="setZoom('200')" href="javascript:void(0)">200%</a></li>
                    <li><a class="dropdown-item" v-on:click="setZoom('300')" href="javascript:void(0)">300%</a></li>
                </ul>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.7.16/vue.min.js"
        integrity="sha512-Wx8niGbPNCD87mSuF0sBRytwW2+2ZFr7HwVDF8krCb3egstCc4oQfig+/cfg2OHd82KcUlOYxlSDAqdHqK5TCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        const app = new Vue({
            el: '#app',
            data: {
                zoom: 'zoom-original',
                role: 0,
                data: @json($data),
                active: @json($active)
            },
            methods: {
                formatLabel(type) {
                    return type.replaceAll('_', ' ').toUpperCase()
                },
                next() {
                    if (this.role < this.data.length - 1)
                        this.role++
                    else this.role = 0
                },
                nnext() {
                    this.role = this.data.length - 1
                },
                prev() {
                    if (this.role <= 0)
                        this.role = this.data.length - 1
                    else this.role--
                },
                pprev() {
                    this.role = 0
                },
                setZoom(zoom) {
                    this.zoom = 'zoom-' + zoom
                },
                isPdf(data) {
                    return data.extension.toLowerCase() === 'pdf';
                },

                isImage(data) {
                    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
                    return imageExtensions.includes(data.extension.toLowerCase());
                },
                isOther(data) {
                    return !this.isPdf(data) && !this.isImage(data);
                },
                openManual(data) {
                    window.open(data.url, 'popup',
                        `width=600,height=600,top=${window.outerHeight/2 - 300},left=${window.outerWidth/2 - 300}`
                    );
                }
            }
        })

        if (app.active) {
            const c = app.data.map(d => d.url).indexOf(app.active.url);
            if (c !== -1) {
                app.role = c;
            }
        }
    </script>
</body>

</html>
