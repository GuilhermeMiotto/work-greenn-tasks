<template>
  <div v-if="visible" class="modal">
    <div class="modal-content">
      <div class="modal-body">
        <div class="modal-input">
          <label for="editTaskName">Nome da Tarefa:</label>
          <input
            type="text"
            id="editTaskName"
            v-model="editedTask.titulo"
            placeholder="Digite o nome da tarefa"
          />
        </div>
        <div class="modal-input">
          <label for="editTaskDescription">Descrição da Tarefa:</label>
          <textarea
            id="editTaskDescription"
            v-model="editedTask.descricao"
            placeholder="Digite a descrição da tarefa"
          ></textarea>
        </div>
        <div class="modal-input">
          <label for="editTaskDueDate">Data de Vencimento:</label>
          <div class="date-input-container">
            <i class="fa-regular fa-calendar-alt"></i>
            <input
              type="text"
              id="editTaskDueDate"
              v-model="editedTask.data_vencimento"
              :class="{ 'valid-date': isDateValid, overdue: !isDateValid }"
              @input="handleDateInput"
              placeholder="DD/MM/AAAA"
            />
          </div>
        </div>
      </div>
      <hr class="modal-divider" />
      <div class="modal-footer">
        <button class="btn-cancel" @click="closeModal">Cancelar</button>
        <button
          class="btn-update-task"
          :class="{ disabled: !isDateValid }"
          @click="updateTaskOnServer"
        >
          Atualizar Tarefa
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    visible: {
      type: Boolean,
      required: true,
    },
    task: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      editedTask: { ...this.task },
      isDateValid: true,
    };
  },
  methods: {
    updateTaskOnServer() {
      if (this.isDateValid) {
        axios
          .put(
            `http://localhost:8000/actions/editar_tarefa.php?id=${this.editedTask.id}`,
            this.editedTask
          )
          .then(() => {
            this.$emit("update-task", this.editedTask);
            this.closeModal();
          })
          .catch((error) => {
            console.error("Erro ao atualizar tarefa:", error);
          });
      } else {
        console.log("A data não é válida, a tarefa não será atualizada.");
      }
    },
    handleDateInput() {
      const regexDate = /^\d{2}\/\d{2}\/\d{4}$/;
      this.isDateValid = regexDate.test(this.editedTask.data_vencimento);

      if (this.isDateValid) {
        const [day, month, year] = this.editedTask.data_vencimento.split("/");
        const taskDate = new Date(`${year}-${month}-${day}`);
        const cutoffDate = new Date();

        this.isDateValid = taskDate >= cutoffDate;
      }
    },
    closeModal() {
      this.$emit("close-modal");
    },
  },
};
</script>
  
  <style scoped>
* {
  margin: 0;
  padding: 0;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: #fff;
  width: 678px;
  max-width: 100%;
  border: 1px solid #ccc;
  border-radius: 8px;
  overflow: hidden;
}

.modal-body {
  padding: 10px;
}

.modal-input {
  margin-bottom: 10px;
}

.modal-input label {
  display: block;
  font-size: 14px;
  margin-bottom: 5px;
  text-align: left;
}

.date-input-container {
  left: 1.35%;
  width: 170px;
}

.modal-input input,
.modal-input textarea {
  width: calc(100% - 16px);
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-top: 5px;
  height: 40px;
  justify-content: center;
}

.fa-calendar-alt {
  font-family: "Font Awesome 5 Free";
  position: absolute;
  left: 543px;
  top: 56%;
  transform: translateY(-50%);
}

.modal-divider {
  border: 0;
  height: 1px;
  background: #828181;
  margin: 10px 0;
}

.modal-footer {
  padding: 10px;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.btn-cancel,
.btn-update-task {
  width: auto;
  height: 40px;
  padding: 8px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-cancel {
  background-color: rgb(212, 212, 212);
  color: #000000;
}

.btn-update-task {
  background-color: #000000;
  color: #fff;
}
</style>
  