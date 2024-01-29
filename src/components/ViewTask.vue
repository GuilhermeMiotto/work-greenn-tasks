<template>
  <div class="centered-page">
    <div class="my-window">
      <div class="navbar">
        <div
          class="left-navbar"
          :class="{ 'invalid-box': !isDateValid, 'valid-box': isDateValid }"
        >
          <div
            :class="{ 'date-valid': isDateValid, 'date-invalid': !isDateValid }"
          >
            <i
              :class="['far', 'fa-calendar', { 'date-valid': isDateValid }]"
              :style="{ color: isDateValid ? '#009488' : '#ff0000' }"
            ></i>
            {{ isDateValid ? "No Prazo" : "Fora do Prazo" }}
          </div>
        </div>
        <div class="right-navbar">
          <i class="fas fa-ellipsis-h" @click="toggleDropdown"></i>
          <div class="dropdown" v-if="showDropdown">
            <div @click="copyTaskLink">
              <i class="far fa-copy"></i> Copiar link da tarefa
            </div>
            <div @click="duplicateTask">
              <i class="far fa-clone"></i> Duplicar tarefa
            </div>
            <div @click="printTask">
              <i class="fas fa-print"></i> Imprimir tarefa
            </div>
            <div @click="deleteTask" style="color: red">
              <i class="fas fa-trash"></i> Excluir tarefa
            </div>
          </div>
          <i class="fas fa-times" @click="closeModal"></i>
        </div>
      </div>
      <div class="content">
        <div class="task-info">
          <input
            type="checkbox"
            class="checkbox"
            v-model="completed"
            @change="updateTaskState"
          />
          <div class="check-icon" v-if="completed">
            <i class="fas fa-check"></i>
          </div>
        </div>

        <p class="titulo">{{ task.titulo }}</p>
        <p class="descricao">{{ task.descricao }}</p>
      </div>
      <div class="rectangle">
        <div>
          <p>
            <span> Criado em:</span> <br />
            <i class="far fa-calendar one"></i>
            {{ formatDate(task.created_at) }}
          </p>
        </div>

        <div>
          <p>
            <span> Data de vencimento:</span> <br />
            <i
              class="far fa-calendar two"
              :style="{
                color: isDateValid ? '#009488' : '#ff0000',
                'margin-right': '5px',
              }"
            ></i>
            <span :class="{ overdue: !isDateValid, 'valid-date': isDateValid }">
              {{ task.data_vencimento }}
            </span>
          </p>
        </div>

        <div>
          <p>
            <span> Modificado em:</span> <br />
            <i class="far fa-calendar three"></i>
            {{ formatDate(task.updated_at) }}
          </p>
        </div>

        <div>
          <p class="four">
            <span> ID da tarefa:</span> <br />
            {{ task.id }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      showDropdown: false,
      isDateValid: true,
      completed: false,
    };
  },
  props: {
    task: Object,
  },
  methods: {
    duplicateTask() {
      axios
        .post(`http://localhost:8000/actions/duplicata.php`, {
          taskId: this.task.id,
        })
        .then(() => {
          this.completed = this.task.completed === "1";
        })
        .catch((error) => {
          console.error("Erro ao duplicar tarefa:", error);
        });
    },
    printTask() {
      window.print();
    },
    deleteTask() {
  axios
    .delete(`http://localhost:8000/actions/excluir_tarefa.php?id=${this.task.id}`)
    .then(() => {
      this.closeModal();
      window.location.reload();
    })
    .catch((error) => {
      console.error("Erro ao excluir tarefa:", error);
    });
},
    closeModal() {
      this.$emit("closeModal");
    },
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },

    formatDate(dateString) {
      const options = {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
      };

      const formattedDate = new Date(dateString).toLocaleString(
        undefined,
        options
      );
      return formattedDate;
    },

    validateDate() {
      const regexDate = /^\d{2}\/\d{2}\/\d{4}$/;
      this.isDateValid = regexDate.test(this.task.data_vencimento);

      if (this.isDateValid) {
        const [day, month, year] = this.task.data_vencimento.split("/");
        const taskDate = new Date(`${year}-${month}-${day}`);
        const cutoffDate = new Date("2024-01-22");

        this.isDateValid = taskDate >= cutoffDate;
      }
    },
  },

  created() {
    this.validateDate();
    this.completed = this.task.completed === "1";
  },
  computed: {
    isTaskOverdue() {
      const taskDueDate = new Date(this.task.date);
      const currentDate = new Date();
      return taskDueDate < currentDate;
    },
  },

  watch: {
    "task.date": "validateDate",
  },
};
</script>

<style scoped>
@import "~@fortawesome/fontawesome-free/css/all.css";

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Montserrat", sans-serif;
}

.far,
.fas {
  font-family: "Font Awesome 5 Free";
}

.centered-page {
  display: flex;
  justify-content: center;
  align-items: center;
}

.my-window {
  width: 819px;
  height: 613px;
  position: absolute;
  top: 12%;
  left: 29%;
  border: 1px solid rgb(179, 176, 176);
  border-radius: 10px;
  background-color: white;
  display: flex;
  flex-direction: column;
  z-index: 1000;
}

.navbar {
  width: 819px;
  height: 66px;
  background-color: #ffffff;
  border: 1px solid rgb(179, 176, 176);
  border-radius: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 15px;
}
.navbar .right-navbar i {
  margin-right: 20px;
  font-size: 20px;
  cursor: pointer;
}

.left-navbar {
  padding: 5px;
  border-radius: 5px;
}

.dropdown {
  position: absolute;
  top: 10%;
  right: 0;
  background-color: #fff;
  border: 1px solid #dfdcdc;
  z-index: 1;
  width: 250px;
  text-align: left;
  cursor: pointer;
}

.dropdown div {
  padding: 10px;
  transition: background-color 0.3s;
}

.dropdown div:hover {
  background-color: #f5f5f5;
}

.rectangle {
  width: 246px;
  height: 545px;
  background-color: whitesmoke;
  position: absolute;
  top: 66px;
  right: 0;
  padding: 20px;
  box-sizing: border-box;
}
.rectangle div {
  padding: 20px;
  text-align: left;
}

.rectangle p {
  margin-bottom: 15px;
  font-size: 14px;
}

.rectangle span {
  font-size: 14px;
}

.content {
  position: relative;
  height: 150px;
  width: 100%;
  max-width: 519px;
  margin: 0 auto;
  padding: 20px;
  top: 5%;
  left: -15%;
  right: 20%;
}

.checkbox {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  display: flex;
  justify-content: flex-start;
  width: 20px;
  height: 20px;
  margin-right: 10px;
  border-radius: 50%;
  border: 2px solid #333;
  outline: none;
  cursor: pointer;
}

.checkbox:checked {
  background-color: #000000;
  border-color: #000000;
}

.check-icon {
  position: absolute;
  top: 15%;
  left: 4.7%;
  color: rgb(255, 255, 255);
  font-size: 12px;
  font-weight: bold;
  pointer-events: none;
}

.titulo {
  font-size: 20px;
  position: absolute;
  left: 14%;
  top: 11.9%;
  font-weight: bold;
}

.descricao {
  word-wrap: break-word;
  max-width: 450px;
  font-size: 17px;
  position: absolute;
  left: 14.2%;
  top: 35%;
}

.overdue {
  color: #ff0000;
}

.valid-date {
  color: #009488;
}

.invalid-box {
  color: #ff0000;
  background-color: rgba(255, 0, 0, 0.1);
  border: 1px solid #ff0000;
  box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
}

.valid-box {
  color: #009488;
  background-color: rgba(0, 255, 0, 0.1);
  border: 1px solid #00ff00;
  box-shadow: 0 0 5px #009488;
}
</style>