(function () {
  "use strict";

  function clone(arr) {
    return JSON.parse(JSON.stringify(arr || []));
  }

  function round2(n) {
    return Math.round(n * 100) / 100;
  }

  function clamp(n, min, max) {
    return Math.max(min, Math.min(max, n));
  }

  function rnd(min, max) {
    return Math.round(min + Math.random() * (max - min));
  }

  window.panel.plugin("cbc/sticker-block", {
    blocks: {
      sticker: {
        data() {
          return {
            selected: null,   // index of the selected sticker, or null
            draft: null,      // working copy of the array during an interaction
            interaction: null, // active drag/rotate/resize state
            urls: {}          // filename -> preview url (resolved, not persisted)
          };
        },
        created() {
          this.loadUrls();
        },
        watch: {
          "content.stickers": function () {
            this.loadUrls();
          }
        },
        computed: {
          // Render from the live draft while interacting, otherwise from content.
          stickers() {
            return this.draft || this.content.stickers || [];
          }
        },
        methods: {
          // Resolve preview URLs for any sticker filenames we don't know yet.
          // Filenames are the source of truth; URLs are never stored in content.
          loadUrls() {
            var self = this;
            var list = this.content.stickers || [];
            var missing = list.some(function (s) {
              return s.image && !self.urls[s.image];
            });
            if (!missing) {
              return;
            }
            this.$api
              .get(this.endpoints.model + "/files", { limit: 500 })
              .then(function (res) {
                var data = (res && res.data) || res || [];
                data.forEach(function (f) {
                  if (f && f.filename) {
                    self.$set(self.urls, f.filename, f.url);
                  }
                });
              })
              .catch(function () {
                /* leave placeholders; non-fatal */
              });
          },
          canvasRect() {
            return this.$refs.canvas.getBoundingClientRect();
          },
          deselect() {
            this.selected = null;
          },
          commit(arr) {
            // Base block component merges this into content and triggers save.
            this.update({ stickers: arr });
          },
          stickerStyle(s, index) {
            return {
              left: s.x + "%",
              top: s.y + "%",
              width: s.width + "%",
              transform: "rotate(" + s.rotation + "deg)",
              zIndex: index + 1
            };
          },

          // --- library / lifecycle -------------------------------------
          addSticker() {
            var self = this;
            this.$panel.dialog.open({
              component: "k-files-dialog",
              props: {
                endpoint: this.endpoints.model + "/files",
                multiple: true
              },
              on: {
                submit: function (files) {
                  self.$panel.dialog.close();
                  if (!files || !files.length) {
                    return;
                  }
                  var arr = clone(self.content.stickers);
                  files.forEach(function (f, i) {
                    self.$set(self.urls, f.filename, f.url);
                    arr.push({
                      image: f.filename,
                      x: 38 + i * 4,
                      y: 38 + i * 4,
                      width: 20,
                      rotation: 0
                    });
                  });
                  self.commit(arr);
                  self.selected = arr.length - 1;
                }
              }
            });
          },
          randomize() {
            var arr = clone(this.content.stickers);
            if (!arr.length) {
              return;
            }
            arr.forEach(function (s) {
              s.width = rnd(12, 34);
              s.x = rnd(-8, 80);
              s.y = rnd(-8, 76);
              s.rotation = rnd(-45, 45);
            });
            this.commit(arr);
          },
          remove(index) {
            var arr = clone(this.content.stickers);
            arr.splice(index, 1);
            this.commit(arr);
            this.selected = null;
          },
          bringForward(index) {
            var arr = clone(this.content.stickers);
            if (index >= arr.length - 1) {
              return;
            }
            arr.splice(index + 1, 0, arr.splice(index, 1)[0]);
            this.commit(arr);
            this.selected = index + 1;
          },
          sendBackward(index) {
            var arr = clone(this.content.stickers);
            if (index <= 0) {
              return;
            }
            arr.splice(index - 1, 0, arr.splice(index, 1)[0]);
            this.commit(arr);
            this.selected = index - 1;
          },

          // --- interactions --------------------------------------------
          startMove(index, event) {
            this.beginInteraction("move", index, event);
          },
          startRotate(index, event) {
            this.beginInteraction("rotate", index, event);
          },
          startResize(index, event) {
            this.beginInteraction("resize", index, event);
          },
          beginInteraction(type, index, event) {
            if (this.disabled) {
              return;
            }
            event.preventDefault();
            event.stopPropagation();
            this.selected = index;
            this.draft = clone(this.content.stickers);

            var s = this.draft[index];
            var el = event.currentTarget.closest(".sticker-edit");
            var box = el.getBoundingClientRect();
            var cx = box.left + box.width / 2;
            var cy = box.top + box.height / 2;

            this.interaction = {
              type: type,
              index: index,
              mouseX: event.clientX,
              mouseY: event.clientY,
              startX: s.x,
              startY: s.y,
              startWidth: s.width,
              startRotation: s.rotation,
              centerX: cx,
              centerY: cy,
              startAngle: Math.atan2(event.clientY - cy, event.clientX - cx)
            };

            this._onMove = this.onMove.bind(this);
            this._onUp = this.onUp.bind(this);
            window.addEventListener("mousemove", this._onMove);
            window.addEventListener("mouseup", this._onUp);
          },
          onMove(event) {
            if (!this.interaction) {
              return;
            }
            var it = this.interaction;
            var rect = this.canvasRect();
            var s = this.draft[it.index];

            if (it.type === "move") {
              var dx = ((event.clientX - it.mouseX) / rect.width) * 100;
              var dy = ((event.clientY - it.mouseY) / rect.height) * 100;
              s.x = clamp(round2(it.startX + dx), -30, 100);
              s.y = clamp(round2(it.startY + dy), -30, 100);
            } else if (it.type === "resize") {
              var dw = ((event.clientX - it.mouseX) / rect.width) * 100;
              s.width = clamp(round2(it.startWidth + dw), 3, 100);
            } else if (it.type === "rotate") {
              var cur = Math.atan2(event.clientY - it.centerY, event.clientX - it.centerX);
              var delta = ((cur - it.startAngle) * 180) / Math.PI;
              s.rotation = Math.round(it.startRotation + delta);
            }
            this.$forceUpdate();
          },
          onUp() {
            window.removeEventListener("mousemove", this._onMove);
            window.removeEventListener("mouseup", this._onUp);
            if (this.draft) {
              this.commit(this.draft);
            }
            this.draft = null;
            this.interaction = null;
          }
        },
        template: `
          <div class="sticker-block">
            <div class="sticker-block__bar">
              <k-button icon="add" variant="filled" size="xs" @click="addSticker">
                Sticker hinzufügen
              </k-button>
              <k-button
                v-if="stickers.length"
                icon="sparkling"
                size="xs"
                variant="filled"
                @click="randomize"
              >
                Zufall
              </k-button>
              <span v-if="stickers.length" class="sticker-block__count">
                {{ stickers.length }} Sticker
              </span>
            </div>

            <div ref="canvas" class="sticker-block__canvas" @mousedown="deselect">
              <div v-if="stickers.length === 0" class="sticker-block__placeholder">
                Noch keine Sticker – auf „Sticker hinzufügen“ klicken,
                dann ziehen, drehen und skalieren.
              </div>

              <div
                v-for="(s, index) in stickers"
                :key="index"
                class="sticker-edit"
                :class="{ 'is-selected': selected === index }"
                :style="stickerStyle(s, index)"
                @mousedown.stop="startMove(index, $event)"
              >
                <img
                  v-if="urls[s.image]"
                  :src="urls[s.image]"
                  class="sticker-edit__img"
                  draggable="false"
                >
                <div v-else class="sticker-edit__missing">{{ s.image }}</div>

                <template v-if="selected === index && !disabled">
                  <span
                    class="sticker-edit__handle sticker-edit__rotate"
                    title="Drehen"
                    @mousedown.stop="startRotate(index, $event)"
                  ></span>
                  <span
                    class="sticker-edit__handle sticker-edit__resize"
                    title="Größe ändern"
                    @mousedown.stop="startResize(index, $event)"
                  ></span>
                </template>
              </div>
            </div>

            <div
              v-if="selected !== null && stickers[selected]"
              class="sticker-block__tools"
            >
              <k-button icon="angle-up" size="xs" @click="bringForward(selected)">Vor</k-button>
              <k-button icon="angle-down" size="xs" @click="sendBackward(selected)">Zurück</k-button>
              <k-button icon="trash" size="xs" theme="negative" @click="remove(selected)">Löschen</k-button>
              <span class="sticker-block__readout">
                x {{ Math.round(stickers[selected].x) }}% ·
                y {{ Math.round(stickers[selected].y) }}% ·
                B {{ Math.round(stickers[selected].width) }}% ·
                {{ Math.round(stickers[selected].rotation) }}°
              </span>
            </div>
          </div>
        `
      }
    }
  });
})();
