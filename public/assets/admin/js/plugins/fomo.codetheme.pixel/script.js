(() => {
    let pixel_url_base = "https://fomo.codedthemes.com/";
    let pixel_title = "CodedThemes";
    let pixel_key = "CDkpF1sQ8Tt5wpMZgqRvKpQiUhpWE3bc";
    let pixel_analytics = true;
    let pixel_css_loaded = false;
    let campaign_domain = "ableproadmin.com";
    if (campaign_domain.startsWith("www.")) {
        campaign_domain = campaign_domain.replace("www.", "");
    }

    /* Make sure the campaign loads only where expected */
    if (
        window.location.hostname !== campaign_domain &&
        window.location.hostname !== `www.${campaign_domain}`
    ) {
        console.log(
            `${pixel_title} (${pixel_url_base}): Campaign does not match the set domain/subdomain.`
        );
        return;
    }

    /* Make sure to include the external css file */
    let link = document.createElement("link");
    link.href =
        "https://fomo.codedthemes.com/themes/altum/assets/css/pixel.min.css";
    link.type = "text/css";
    link.rel = "stylesheet";
    link.media = "screen,print";
    link.onload = function () {
        pixel_css_loaded = true;
    };
    document.getElementsByTagName("head")[0].appendChild(link);

    /* Pixel header including all the needed code */
    let send_tracking_data = (t) => {
            if (
                !(
                    t.subtype &&
                    ["impression", "click", "hover"].includes(t.subtype)
                ) ||
                pixel_analytics
            ) {
                t.url = window.location.href;
                try {
                    navigator.sendBeacon(
                        `${pixel_url_base}pixel-track/${pixel_key}`,
                        JSON.stringify(t)
                    );
                } catch (i) {
                    console.log(`${pixel_title} (${pixel_url_base}): ${i}`);
                }
            }
        },
        get_scroll_percentage = () => {
            let t = document.documentElement,
                i = document.body,
                e = "scrollTop",
                o = "scrollHeight";
            return ((t[e] || i[e]) / ((t[o] || i[o]) - t.clientHeight)) * 100;
        };
    class AltumCodeManager {
        constructor(t) {
            (this.options = {}),
                (this.options.content = t.content || ""),
                (this.options.should_show =
                    void 0 === t.should_show || t.should_show),
                (this.options.delay = void 0 === t.delay ? 3e3 : t.delay),
                (this.options.duration =
                    void 0 === t.duration ? 3e3 : t.duration),
                (this.options.selector = t.selector),
                (this.options.url = t.url),
                (this.options.url_new_tab =
                    void 0 === t.url_new_tab || t.url_new_tab),
                (this.options.close = void 0 !== t.close && t.close),
                (this.options.stop_on_focus = !0),
                (this.options.position =
                    void 0 === t.position ? "bottom_left" : t.position),
                (this.options.trigger_all_pages =
                    void 0 === t.trigger_all_pages || t.trigger_all_pages),
                (this.options.triggers = t.triggers || []),
                (this.options.display_frequency =
                    void 0 === t.display_frequency
                        ? "all_time"
                        : t.display_frequency),
                (this.options.display_mobile =
                    void 0 === t.display_mobile || t.display_mobile),
                (this.options.display_desktop =
                    void 0 === t.display_desktop || t.display_desktop),
                (this.options.display_trigger =
                    void 0 === t.display_trigger ? "delay" : t.display_trigger),
                (this.options.display_trigger_value =
                    void 0 === t.display_trigger_value
                        ? 3
                        : t.display_trigger_value),
                (this.options.display_delay_type_after_close =
                    void 0 === t.display_delay_type_after_close
                        ? "time_on_site"
                        : t.display_delay_type_after_close),
                (this.options.display_delay_value_after_close =
                    void 0 === t.display_delay_value_after_close
                        ? 21600
                        : t.display_delay_value_after_close),
                (this.options.data_trigger_auto =
                    void 0 !== t.data_trigger_auto && t.data_trigger_auto),
                (this.options.data_triggers_auto = t.data_triggers_auto || []),
                (this.options.on_animation =
                    void 0 === t.on_animation ? "fadeIn" : t.on_animation),
                (this.options.off_animation =
                    void 0 === t.off_animation ? "fadeOut" : t.off_animation),
                (this.options.animation =
                    void 0 !== t.animation && t.animation),
                (this.options.animation_interval =
                    void 0 === t.animation_interval ? 5 : t.animation_interval),
                (this.options.notification_id = t.notification_id || !1);
        }
        build() {
            if (
                (this.options.data_trigger_auto &&
                    this.is_page_triggered(this.options.data_triggers_auto) &&
                    document.querySelectorAll("form").forEach((t) => {
                        !t.getAttribute(
                            `data-${pixel_key}-${this.options.notification_id}-form`
                        ) &&
                            (t.addEventListener("submit", (i) => {
                                let e = {};
                                t.querySelectorAll("input").forEach((t) => {
                                    "password" != t.type &&
                                        "hidden" != t.type &&
                                        -1 === t.name.indexOf("captcha") &&
                                        (e[`form_${t.name}`] = t.value);
                                }),
                                    send_tracking_data({
                                        ...e,
                                        notification_id:
                                            this.options.notification_id,
                                        page_title: document.title,
                                        type: "auto_capture",
                                    });
                            }),
                            t.setAttribute(
                                `data-${pixel_key}-${this.options.notification_id}-form`,
                                !0
                            ));
                    }),
                !this.options.should_show ||
                    (!this.options.trigger_all_pages &&
                        !this.is_page_triggered(this.options.triggers)))
            )
                return !1;
            switch (this.options.display_frequency) {
                case "all_time":
                    break;
                case "once_per_session":
                    if (
                        sessionStorage.getItem(
                            `notification_display_frequency_${this.options.notification_id}`
                        )
                    )
                        return !1;
                    break;
                case "once_per_browser":
                    if (
                        localStorage.getItem(
                            `notification_display_frequency_${this.options.notification_id}`
                        )
                    )
                        return !1;
            }
            if (
                (!this.options.display_mobile && window.innerWidth < 768) ||
                (!this.options.display_desktop && window.innerWidth > 768)
            )
                return !1;
            if (
                sessionStorage.getItem(
                    `notification_manually_closed_${this.options.notification_id}`
                )
            )
                switch (this.options.display_delay_type_after_close) {
                    case "time_on_site":
                        if (
                            parseInt(
                                sessionStorage.getItem(
                                    `notification_display_delay_type_after_close_time_on_site_${this.options.notification_id}`
                                ) ?? 0
                            ) <
                            1e3 * this.options.display_delay_value_after_close
                        )
                            return (
                                setInterval(() => {
                                    let t = parseInt(
                                        sessionStorage.getItem(
                                            `notification_display_delay_type_after_close_time_on_site_${this.options.notification_id}`
                                        ) ?? 0
                                    );
                                    sessionStorage.setItem(
                                        `notification_display_delay_type_after_close_time_on_site_${this.options.notification_id}`,
                                        t + 500
                                    );
                                }, 500),
                                !1
                            );
                        break;
                    case "pageviews":
                        let t =
                            parseInt(
                                sessionStorage.getItem(
                                    `notification_display_delay_type_after_close_pageviews_${this.options.notification_id}`
                                ) ?? 0
                            ) + 1;
                        if (
                            (sessionStorage.setItem(
                                `notification_display_delay_type_after_close_pageviews_${this.options.notification_id}`,
                                t
                            ),
                            t < this.options.display_delay_value_after_close)
                        )
                            return !1;
                }
            let i = document.createElement("div");
            if (
                ((i.className = "altumcode"),
                (i.className += ` altumcode-${this.options.position}`),
                i.setAttribute("data-position", this.options.position),
                i.setAttribute("data-on-animation", this.options.on_animation),
                i.setAttribute(
                    "data-off-animation",
                    this.options.off_animation
                ),
                i.setAttribute(
                    "data-notification-id",
                    this.options.notification_id
                ),
                (i.innerHTML = this.options.content),
                this.options.close)
            ) {
                let e = i.querySelector('button[class="altumcode-close"]');
                e &&
                    ((e.innerHTML = "\xd7"),
                    e.addEventListener("click", (t) => {
                        t.stopPropagation(),
                            sessionStorage.setItem(
                                `notification_manually_closed_${this.options.notification_id}`,
                                !0
                            ),
                            sessionStorage.removeItem(
                                `notification_display_delay_type_after_close_time_on_site_${this.options.notification_id}`
                            ),
                            sessionStorage.removeItem(
                                `notification_display_delay_type_after_close_pageviews_${this.options.notification_id}`
                            ),
                            this.constructor.remove_notification(i);
                    }));
            } else
                i.querySelector('button[class="altumcode-close"]') &&
                    (i.querySelector(
                        'button[class="altumcode-close"]'
                    ).innerHTML = "");
            void 0 !== this.options.url &&
                "" !== this.options.url &&
                (i.classList.add("altumcode-clickable"),
                i.addEventListener("click", (t) => {
                    this.options.notification_id &&
                        send_tracking_data({
                            notification_id: this.options.notification_id,
                            type: "notification",
                            subtype: "click",
                        }),
                        this.options.url_new_tab
                            ? window.open(this.options.url, "_blank")
                            : (window.location = this.options.url),
                        t.stopPropagation();
                }));
            let o = i.querySelector(".altumcode-site");
            return (
                o &&
                    o.addEventListener("click", (t) => {
                        let i = t.currentTarget.href;
                        window.open(i, "_blank"),
                            t.stopPropagation(),
                            t.preventDefault();
                    }),
                i
            );
        }
        initiate(t = {}) {
            let i = () => {
                let i = null;
                i = setInterval(() => {
                    pixel_css_loaded && (clearInterval(i), this.process(t));
                }, 100);
            };
            "complete" !== document.readyState &&
            ("loading" === document.readyState ||
                document.documentElement.doScroll)
                ? document.addEventListener("DOMContentLoaded", () => {
                      i();
                  })
                : i();
            let e = location.href;
            setInterval(() => {
                if (e != location.href) {
                    e = location.href;
                    let t = document.querySelector(
                        `[data-notification-id="${this.options.notification_id}"]`
                    );
                    this.constructor.remove_notification(t), i();
                }
            }, 750);
        }
        process(t = {}) {
            let i = this.build();
            if (!i) return !1;
            switch (this.options.position) {
                case "top":
                case "top_floating":
                    document.body.prepend(i);
                    break;
                default:
                    document.body.appendChild(i);
            }
            let e = () => {
                switch (
                    ((i.style.display = "block"),
                    i.classList.add(`on-${this.options.on_animation}`),
                    i.classList.add("on-visible"),
                    setTimeout(() => {
                        i.classList.remove(`on-${this.options.on_animation}`);
                    }, 1500),
                    this.constructor.reposition(),
                    t.displayed && t.displayed(i),
                    this.options.animation &&
                        (i.animation_interval = window.setInterval(() => {
                            i.classList.add(
                                `animation-${this.options.animation}`
                            ),
                                setTimeout(() => {
                                    i.classList.remove(
                                        `animation-${this.options.animation}`
                                    );
                                }, (this.options.animation_interval - 1) * 1e3);
                        }, 1e3 * this.options.animation_interval)),
                    -1 !== this.options.duration &&
                        (i.timeout = window.setTimeout(() => {
                            this.constructor.remove_notification(i);
                        }, this.options.duration)),
                    this.options.stop_on_focus &&
                        -1 !== this.options.duration &&
                        (i.addEventListener("mouseover", (t) => {
                            window.clearTimeout(i.timeout);
                        }),
                        i.addEventListener("mouseleave", () => {
                            i.timeout = window.setTimeout(() => {
                                this.constructor.remove_notification(i);
                            }, this.options.duration);
                        })),
                    this.options.display_frequency)
                ) {
                    case "all_time":
                        break;
                    case "once_per_session":
                        sessionStorage.setItem(
                            `notification_display_frequency_${this.options.notification_id}`,
                            !0
                        );
                        break;
                    case "once_per_browser":
                        localStorage.setItem(
                            `notification_display_frequency_${this.options.notification_id}`,
                            !0
                        );
                }
                this.options.notification_id &&
                    (send_tracking_data({
                        notification_id: this.options.notification_id,
                        type: "notification",
                        subtype: "impression",
                    }),
                    i.addEventListener("mouseover", () => {
                        sessionStorage.getItem(
                            `notification_hover_${this.options.notification_id}`
                        ) ||
                            (send_tracking_data({
                                notification_id: this.options.notification_id,
                                type: "notification",
                                subtype: "hover",
                            }),
                            sessionStorage.setItem(
                                `notification_hover_${this.options.notification_id}`,
                                !0
                            ));
                    })),
                    window.removeEventListener(
                        "resize",
                        this.constructor.reposition
                    ),
                    window.addEventListener(
                        "resize",
                        this.constructor.reposition
                    );
            };
            switch (this.options.display_trigger) {
                case "delay":
                    setTimeout(() => {
                        e();
                    }, 1e3 * this.options.display_trigger_value);
                    break;
                case "time_on_site":
                    let o = () => {
                            let t = parseInt(
                                sessionStorage.getItem(
                                    `notification_display_trigger_time_on_site_${this.options.notification_id}`
                                ) ?? 0
                            );
                            t > 1e3 * this.options.display_trigger_value &&
                                (e(), clearInterval(s)),
                                sessionStorage.setItem(
                                    `notification_display_trigger_time_on_site_${this.options.notification_id}`,
                                    t + 500
                                );
                        },
                        s = setInterval(o, 500);
                    o();
                    break;
                case "inactivity":
                    let n = null,
                        a = !1,
                        r = () => {
                            a ||
                                (clearTimeout(n),
                                (n = setTimeout(() => {
                                    e(),
                                        (a = !0),
                                        [
                                            "load",
                                            "mousemove",
                                            "mousedown",
                                            "touchstart",
                                            "touchmove",
                                            "click",
                                            "keydown",
                                            "scroll",
                                            "wheel",
                                        ].forEach((t) => {
                                            window.removeEventListener(
                                                t,
                                                r,
                                                !0
                                            );
                                        });
                                }, 1e3 * this.options.display_trigger_value)));
                        };
                    [
                        "load",
                        "mousemove",
                        "mousedown",
                        "touchstart",
                        "touchmove",
                        "click",
                        "keydown",
                        "scroll",
                        "wheel",
                    ].forEach((t) => {
                        window.addEventListener(t, r, !0);
                    });
                    break;
                case "pageviews":
                    let l =
                        parseInt(
                            sessionStorage.getItem(
                                `notification_display_trigger_pageviews_${this.options.notification_id}`
                            ) ?? 0
                        ) + 1;
                    sessionStorage.setItem(
                        `notification_display_trigger_pageviews_${this.options.notification_id}`,
                        l
                    ),
                        l >= this.options.display_trigger_value && e();
                    break;
                case "exit_intent":
                    let c = !1;
                    document.addEventListener("mouseout", (t) => {
                        let i = Math.max(
                            document.documentElement.clientWidth,
                            window.innerWidth || 0
                        );
                        if (!(t.clientX >= i - 50) && !(t.clientY >= 50))
                            t.relatedTarget ||
                                t.toElement ||
                                c ||
                                (e(), (c = !0));
                    });
                    break;
                case "scroll":
                    let d = !1;
                    document.addEventListener("scroll", (t) => {
                        !d &&
                            get_scroll_percentage() >
                                this.options.display_trigger_value &&
                            (e(), (d = !0));
                    });
                    break;
                case "click":
                    document.querySelector(
                        this.options.display_trigger_value
                    ) &&
                        document
                            .querySelector(this.options.display_trigger_value)
                            .addEventListener("click", (t) => {
                                e();
                            });
                    break;
                case "hover":
                    document.querySelector(
                        this.options.display_trigger_value
                    ) &&
                        document
                            .querySelector(this.options.display_trigger_value)
                            .addEventListener("mouseenter", (t) => {
                                e();
                            });
            }
        }
        is_page_triggered(t) {
            let i = !1;
            for (let e of t)
                if (e.type.startsWith("not_")) {
                    i = !0;
                    break;
                }
            return (
                t.forEach((t) => {
                    switch (t.type) {
                        case "exact":
                            t.value == window.location.href && (i = !0);
                            break;
                        case "not_exact":
                            t.value == window.location.href && (i = !1);
                            break;
                        case "contains":
                            window.location.href.includes(t.value) && (i = !0);
                            break;
                        case "not_contains":
                            window.location.href.includes(t.value) && (i = !1);
                            break;
                        case "starts_with":
                            window.location.href.startsWith(t.value) &&
                                (i = !0);
                            break;
                        case "not_starts_with":
                            window.location.href.startsWith(t.value) &&
                                (i = !1);
                            break;
                        case "ends_with":
                            window.location.href.endsWith(t.value) && (i = !0);
                            break;
                        case "not_ends_with":
                            window.location.href.endsWith(t.value) && (i = !1);
                            break;
                        case "page_contains":
                            document.body.innerText.includes(t.value) &&
                                (i = !0);
                    }
                }),
                i
            );
        }
        static remove_notification(t) {
            try {
                t.getAttribute("data-on-animation");
                let i = t.getAttribute("data-off-animation");
                t.classList.add(`off-${i}`),
                    window.setTimeout(() => {
                        t.parentNode.removeChild(t),
                            AltumCodeManager.reposition();
                    }, 400);
            } catch (e) {}
        }
        static reposition() {
            let t = document.querySelectorAll(
                    'div[class*="altumcode"][class*="on-"]'
                ),
                i = Math.floor(
                    (window.innerHeight > 0
                        ? window.innerHeight
                        : screen.height) / 2
                ),
                e = {
                    top_left: { left: 20, top: 20 },
                    top_center: { top: 20 },
                    top_right: { right: 20, top: 20 },
                    middle_left: { left: 20, top: i },
                    middle_center: { top: i },
                    middle_right: { right: 20, top: i },
                    bottom_left: { left: 20, bottom: 20 },
                    bottom_center: { bottom: 20 },
                    bottom_right: { right: 20, bottom: 20 },
                };
            for (let o = t.length - 1; o >= 0; o--) {
                let s = t[o].getAttribute("data-position"),
                    n = t[o].offsetHeight;
                switch (s) {
                    default:
                        continue;
                    case "top_left":
                    case "top_center":
                    case "top_right":
                        (t[o].style.top = `${e[s].top}px`),
                            (e[s].top += n + 20);
                        break;
                    case "middle_left":
                    case "middle_center":
                    case "middle_right":
                        (t[o].style.top = `${e[s].top - n / 2}px`),
                            (e[s].top += n + 20);
                        break;
                    case "bottom_left":
                    case "bottom_center":
                    case "bottom_right":
                        (t[o].style.bottom = `${e[s].bottom}px`),
                            (e[s].bottom += n + 20);
                }
            }
        }
    }

    new AltumCodeManager({
        content:
            '\n\n<div id="notification_71" role="dialog" class="altumcode-wrapper altumcode-wrapper-straight    altumcode-button-bar-wrapper" style=\'font-family: inherit!important;background-color: #101010;border-width: 0px;border-color: #000;;padding: 12px !important;;--shadow-r: 0;--shadow-g: 0;--shadow-b: 0;\'>\n    <div class="altumcode-button-bar-content">\n        <div class="altumcode-button-bar-row">\n            <p class="altumcode-button-bar-title" style="color: #FFFFFF">Unlock Able Pro 14-in-1 Bundle at 30% OFF </p>\n\n            <div class="altumcode-button-bar-middle">\n                <button type="button" class="altumcode-button-bar-button" style="color: #FFFFFF;background: #4680FF;">Use Code - ABLE30</button>\n\n                <button class="altumcode-close" style="color: #808080;">\u00d7</button>\n            </div>\n        </div>\n\n                        <a href="https://fomo.codedthemes.com/" class="altumcode-site" style="display: none !important;"></a>\n                </div>\n</div>\n',
        display_mobile: 1,
        display_desktop: 1,
        display_trigger: "delay",
        display_trigger_value: 2,
        display_delay_type_after_close: "time_on_site",
        display_delay_value_after_close: 21600,
        duration: -1,
        url: "https://codedthemes.com/item/able-pro-dashboard-templates",
        url_new_tab: 1,
        close: 1,
        display_frequency: "all_time",
        position: "bottom_floating",
        trigger_all_pages: 0,
        triggers: [
            {
                type: "starts_with",
                value: "https://ableproadmin.com/bootstrap/free/",
            },
            {
                type: "starts_with",
                value: "https://ableproadmin.com/vue/free/",
            },
            {
                type: "starts_with",
                value: "https://ableproadmin.com/angular/free/",
            },
            {
                type: "starts_with",
                value: "https://ableproadmin.com/react/free/",
            },
            {
                type: "starts_with",
                value: "https://ableproadmin.com/tailwind/free",
            },
        ],
        on_animation: "fadeIn",
        off_animation: "fadeOut",
        animation: "",
        animation_interval: 3,

        notification_id: 71,
    }).initiate();

    /* Send basic tracking data */
    send_tracking_data({ type: "track" });
})();
